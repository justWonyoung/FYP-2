<?php

namespace App\Services;

use App\Models\CustomerOrder;
use App\Models\Production;
use App\Models\ProductionSchedulingSetting;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class ProductionSchedulingService
{
    /**
     * @return array{setting: ProductionSchedulingSetting|null, recommendations: array<int, array<string, mixed>>, summary: array{daily_capacity: float|null, unit: string|null, eligible_items: int, total_remaining_quantity: float, estimated_production_days: int, overdue_items: int, urgent_items: int}, warning: string|null}
     */
    public function recommend(?CarbonInterface $today = null): array
    {
        $today = ($today ?? now())->startOfDay();
        $setting = ProductionSchedulingSetting::query()
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->first();

        if ($setting === null || (float) $setting->daily_capacity <= 0) {
            return $this->emptyRecommendation($setting, 'No active production capacity is configured. Configure Production Capacity before generating recommendations.');
        }

        $orders = CustomerOrder::query()
            ->select(['id', 'customer_id', 'order_no', 'order_date', 'due_date', 'priority', 'status'])
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->with([
                'customer:id,customer_name',
                'items:id,customer_order_id,product_name,quantity',
            ])
            ->get();

        $productions = Production::query()
            ->select(['production_id', 'customer_order_no', 'product_name', 'actual_output', 'status'])
            ->whereIn('customer_order_no', $orders->pluck('order_no'))
            ->where('status', '!=', 'cancelled')
            ->get();

        $producedQuantities = [];
        $inProgressItems = [];

        foreach ($productions as $production) {
            $key = $this->itemKey($production->customer_order_no, $production->product_name);
            $producedQuantities[$key] = ($producedQuantities[$key] ?? 0.0) + (float) $production->actual_output;

            if ($production->status === 'in_progress') {
                $inProgressItems[$key] = true;
            }
        }

        $eligibleItems = [];

        foreach ($orders as $order) {
            foreach ($order->items->groupBy('product_name') as $productName => $items) {
                $key = $this->itemKey($order->order_no, $productName);
                $orderedQuantity = (float) $items->sum('quantity');
                $producedQuantity = $producedQuantities[$key] ?? 0.0;
                $remainingQuantity = max(0.0, $orderedQuantity - $producedQuantity);

                if ($remainingQuantity <= 0) {
                    continue;
                }

                $priority = in_array($order->priority, ['normal', 'high', 'urgent'], true)
                    ? $order->priority
                    : 'normal';

                $eligibleItems[] = [
                    'order_no' => $order->order_no,
                    'customer_name' => $order->customer?->customer_name ?? 'Unknown Customer',
                    'product_name' => $productName,
                    'priority' => $priority,
                    'due_date' => $order->due_date?->toDateString(),
                    'order_date' => $order->order_date->toDateString(),
                    'ordered_quantity' => $orderedQuantity,
                    'produced_quantity' => $producedQuantity,
                    'remaining_quantity' => $remainingQuantity,
                    'is_overdue' => $order->due_date !== null && $order->due_date->lt($today),
                    'has_in_progress_production' => isset($inProgressItems[$key]),
                ];
            }
        }

        usort($eligibleItems, fn (array $left, array $right): int => $this->compareItems($left, $right));

        $dailyCapacity = (float) $setting->daily_capacity;
        $recommendations = [];
        $dailyUsage = [];
        $remainingDayCapacity = $dailyCapacity;
        $recommendedDate = $today->copy();

        foreach ($eligibleItems as $item) {
            $remainingItemQuantity = $item['remaining_quantity'];

            while ($remainingItemQuantity > 0) {
                if ($remainingDayCapacity <= 0) {
                    $recommendedDate = $recommendedDate->copy()->addDay();
                    $remainingDayCapacity = $dailyCapacity;
                }

                $recommendedQuantity = min($remainingItemQuantity, $remainingDayCapacity);
                $date = $recommendedDate->toDateString();
                $dailyUsage[$date] = ($dailyUsage[$date] ?? 0.0) + $recommendedQuantity;

                $recommendations[] = [
                    ...$item,
                    'recommended_date' => $date,
                    'recommended_quantity' => $recommendedQuantity,
                    'daily_capacity_used' => 0.0,
                    'reason' => $this->reasonFor($item),
                ];

                $remainingItemQuantity -= $recommendedQuantity;
                $remainingDayCapacity -= $recommendedQuantity;
            }
        }

        foreach ($recommendations as &$recommendation) {
            $recommendation['daily_capacity_used'] = $dailyUsage[$recommendation['recommended_date']];
        }
        unset($recommendation);

        return [
            'setting' => $setting,
            'recommendations' => $recommendations,
            'summary' => [
                'daily_capacity' => $dailyCapacity,
                'unit' => $setting->unit,
                'eligible_items' => count($eligibleItems),
                'total_remaining_quantity' => (float) array_sum(array_column($eligibleItems, 'remaining_quantity')),
                'estimated_production_days' => count($dailyUsage),
                'overdue_items' => count(array_filter($eligibleItems, fn (array $item): bool => $item['is_overdue'])),
                'urgent_items' => count(array_filter($eligibleItems, fn (array $item): bool => $item['priority'] === 'urgent')),
            ],
            'warning' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $left
     * @param  array<string, mixed>  $right
     */
    private function compareItems(array $left, array $right): int
    {
        if ($left['is_overdue'] !== $right['is_overdue']) {
            return $left['is_overdue'] ? -1 : 1;
        }

        if ($left['is_overdue']) {
            $overdueDateComparison = strcmp((string) $left['due_date'], (string) $right['due_date']);

            if ($overdueDateComparison !== 0) {
                return $overdueDateComparison;
            }
        }

        $priorityComparison = $this->priorityRank((string) $left['priority']) <=> $this->priorityRank((string) $right['priority']);

        if ($priorityComparison !== 0) {
            return $priorityComparison;
        }

        $dueDateComparison = $this->compareNullableDates($left['due_date'], $right['due_date']);

        if ($dueDateComparison !== 0) {
            return $dueDateComparison;
        }

        if ($left['has_in_progress_production'] !== $right['has_in_progress_production']) {
            return $left['has_in_progress_production'] ? -1 : 1;
        }

        $orderDateComparison = strcmp((string) $left['order_date'], (string) $right['order_date']);

        if ($orderDateComparison !== 0) {
            return $orderDateComparison;
        }

        $orderNumberComparison = strcmp((string) $left['order_no'], (string) $right['order_no']);

        return $orderNumberComparison !== 0
            ? $orderNumberComparison
            : strcmp((string) $left['product_name'], (string) $right['product_name']);
    }

    private function priorityRank(string $priority): int
    {
        return match ($priority) {
            'urgent' => 0,
            'high' => 1,
            default => 2,
        };
    }

    private function compareNullableDates(mixed $left, mixed $right): int
    {
        if ($left === $right) {
            return 0;
        }

        if ($left === null) {
            return 1;
        }

        if ($right === null) {
            return -1;
        }

        return strcmp((string) $left, (string) $right);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function reasonFor(array $item): string
    {
        if ($item['is_overdue']) {
            return 'Overdue order with the earliest missed due date';
        }

        if ($item['priority'] === 'urgent') {
            return $item['due_date'] !== null
                ? 'Urgent priority with due date '.Carbon::createFromFormat('Y-m-d', $item['due_date'])->format('d M Y')
                : 'Urgent priority';
        }

        if ($item['priority'] === 'high') {
            if ($item['has_in_progress_production'] && $item['due_date'] !== null) {
                return 'High priority; production already in progress, due '.Carbon::createFromFormat('Y-m-d', $item['due_date'])->format('d M Y');
            }

            if ($item['has_in_progress_production']) {
                return 'High priority; production already in progress';
            }

            return 'High priority';
        }

        if ($item['has_in_progress_production']) {
            return $item['due_date'] !== null
                ? 'Production already in progress; due '.Carbon::createFromFormat('Y-m-d', $item['due_date'])->format('d M Y')
                : 'Production already in progress';
        }

        if ($item['due_date'] !== null) {
            return 'Scheduled next based on earliest due date';
        }

        return 'Oldest waiting order after higher-priority items';
    }

    private function itemKey(string $orderNumber, string $productName): string
    {
        return $orderNumber."\0".$productName;
    }

    /**
     * @return array{setting: ProductionSchedulingSetting|null, recommendations: array<int, array<string, mixed>>, summary: array{daily_capacity: float|null, unit: string|null, eligible_items: int, total_remaining_quantity: float, estimated_production_days: int, overdue_items: int, urgent_items: int}, warning: string}
     */
    private function emptyRecommendation(?ProductionSchedulingSetting $setting, string $warning): array
    {
        return [
            'setting' => $setting,
            'recommendations' => [],
            'summary' => [
                'daily_capacity' => null,
                'unit' => null,
                'eligible_items' => 0,
                'total_remaining_quantity' => 0.0,
                'estimated_production_days' => 0,
                'overdue_items' => 0,
                'urgent_items' => 0,
            ],
            'warning' => $warning,
        ];
    }
}

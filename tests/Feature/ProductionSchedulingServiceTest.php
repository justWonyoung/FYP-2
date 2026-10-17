<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerOrder;
use App\Models\Production;
use App\Models\ProductionSchedulingSetting;
use App\Services\ProductionSchedulingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSchedulingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelled_and_completed_orders_are_excluded(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-CANCELLED', 'cancelled');
        $this->createOrder('CO-COMPLETED', 'completed');

        $result = $this->recommend();

        $this->assertSame([], $result['recommendations']);
        $this->assertSame(0, $result['summary']['eligible_items']);
    }

    public function test_zero_remaining_items_are_excluded_and_remaining_quantity_never_becomes_negative(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-FULFILLED', 'pending', items: [['product_name' => 'Serum', 'quantity' => 100]]);
        $this->createProduction('CO-FULFILLED', 'Serum', 150, 'completed');

        $result = $this->recommend();

        $this->assertSame([], $result['recommendations']);
        $this->assertSame(0.0, $result['summary']['total_remaining_quantity']);
    }

    public function test_multiple_production_batches_aggregate_actual_output(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-BATCHES', 'pending', items: [['product_name' => 'Lotion', 'quantity' => 100]]);
        $this->createProduction('CO-BATCHES', 'Lotion', 20, 'completed');
        $this->createProduction('CO-BATCHES', 'Lotion', 30, 'in_progress');

        $recommendation = $this->recommend()['recommendations'][0];

        $this->assertSame(50.0, $recommendation['produced_quantity']);
        $this->assertSame(50.0, $recommendation['remaining_quantity']);
    }

    public function test_cancelled_production_output_is_ignored_but_in_progress_output_counts(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-PRODUCTION', 'pending', items: [['product_name' => 'Toner', 'quantity' => 100]]);
        $this->createProduction('CO-PRODUCTION', 'Toner', 70, 'cancelled');
        $this->createProduction('CO-PRODUCTION', 'Toner', 25, 'in_progress');

        $recommendation = $this->recommend()['recommendations'][0];

        $this->assertSame(25.0, $recommendation['produced_quantity']);
        $this->assertSame(75.0, $recommendation['remaining_quantity']);
        $this->assertTrue($recommendation['has_in_progress_production']);
    }

    public function test_duplicate_order_item_product_names_are_aggregated(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-DUPLICATE', 'pending', items: [
            ['product_name' => 'Cleanser', 'quantity' => 40],
            ['product_name' => 'Cleanser', 'quantity' => 60],
        ]);
        $this->createProduction('CO-DUPLICATE', 'Cleanser', 20, 'completed');

        $recommendation = $this->recommend()['recommendations'][0];

        $this->assertSame(100.0, $recommendation['ordered_quantity']);
        $this->assertSame(80.0, $recommendation['remaining_quantity']);
    }

    public function test_overdue_items_rank_before_all_other_items(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-URGENT', 'pending', 'urgent', '2026-10-20');
        $this->createOrder('CO-OVERDUE', 'pending', 'normal', '2026-10-10');

        $recommendation = $this->recommend()['recommendations'][0];

        $this->assertSame('CO-OVERDUE', $recommendation['order_no']);
        $this->assertSame('Overdue order with the earliest missed due date', $recommendation['reason']);
    }

    public function test_urgent_priority_ranks_before_high_and_normal_when_no_order_is_overdue(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-NORMAL', 'pending', 'normal');
        $this->createOrder('CO-HIGH', 'pending', 'high');
        $this->createOrder('CO-URGENT', 'pending', 'urgent');

        $orderNumbers = array_column($this->recommend()['recommendations'], 'order_no');

        $this->assertSame(['CO-URGENT', 'CO-HIGH', 'CO-NORMAL'], $orderNumbers);
    }

    public function test_earlier_due_date_ranks_before_later_due_date_within_the_same_priority(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-LATER', 'pending', 'normal', '2026-10-24');
        $this->createOrder('CO-EARLIER', 'pending', 'normal', '2026-10-18');

        $orderNumbers = array_column($this->recommend()['recommendations'], 'order_no');

        $this->assertSame(['CO-EARLIER', 'CO-LATER'], $orderNumbers);
    }

    public function test_in_progress_production_is_a_later_tie_breaker(): void
    {
        $this->createCapacity();
        $this->createOrder('CO-WAITING', 'pending', 'normal', '2026-10-20', '2026-10-10');
        $this->createOrder('CO-RUNNING', 'pending', 'normal', '2026-10-20', '2026-10-10');
        $this->createProduction('CO-RUNNING', 'Product', 0, 'in_progress');

        $recommendation = $this->recommend()['recommendations'][0];

        $this->assertSame('CO-RUNNING', $recommendation['order_no']);
        $this->assertSame('Production already in progress; due 20 Oct 2026', $recommendation['reason']);
    }

    public function test_remaining_quantities_split_across_days_without_exceeding_daily_capacity(): void
    {
        $this->createCapacity(800);
        $this->createOrder('CO-FIRST', 'pending', 'normal', null, '2026-10-01', [['product_name' => 'Product', 'quantity' => 500]]);
        $this->createOrder('CO-SECOND', 'pending', 'normal', null, '2026-10-02', [['product_name' => 'Product', 'quantity' => 700]]);

        $recommendations = $this->recommend()['recommendations'];

        $this->assertSame([500.0, 300.0, 400.0], array_column($recommendations, 'recommended_quantity'));
        $this->assertSame(['2026-10-17', '2026-10-17', '2026-10-18'], array_column($recommendations, 'recommended_date'));

        foreach (array_count_values(array_column($recommendations, 'recommended_date')) as $date => $count) {
            $dailyTotal = array_sum(array_map(
                fn (array $recommendation): float => $recommendation['recommended_date'] === $date ? $recommendation['recommended_quantity'] : 0.0,
                $recommendations
            ));

            $this->assertLessThanOrEqual(800.0, $dailyTotal);
        }
    }

    public function test_no_active_capacity_returns_configuration_warning(): void
    {
        $this->createOrder('CO-NO-CAPACITY', 'pending');

        $result = $this->recommend();

        $this->assertSame([], $result['recommendations']);
        $this->assertSame('No active production capacity is configured. Configure Production Capacity before generating recommendations.', $result['warning']);
    }

    private function recommend(): array
    {
        return app(ProductionSchedulingService::class)->recommend(Carbon::parse('2026-10-17'));
    }

    private function createCapacity(float $dailyCapacity = 800): ProductionSchedulingSetting
    {
        return ProductionSchedulingSetting::query()->create([
            'daily_capacity' => $dailyCapacity,
            'unit' => 'pcs',
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<int, array{product_name: string, quantity: int}>  $items
     */
    private function createOrder(
        string $orderNumber,
        string $status,
        string $priority = 'normal',
        ?string $dueDate = null,
        string $orderDate = '2026-10-01',
        array $items = [['product_name' => 'Product', 'quantity' => 100]],
    ): CustomerOrder {
        $customer = Customer::query()->create([
            'customer_name' => 'Scheduling Customer '.str_replace('CO-', '', $orderNumber),
        ]);

        $order = CustomerOrder::query()->create([
            'customer_id' => $customer->id,
            'order_no' => $orderNumber,
            'order_date' => $orderDate,
            'due_date' => $dueDate,
            'priority' => $priority,
            'status' => $status,
            'total_amount' => 0,
        ]);

        foreach ($items as $item) {
            $order->items()->create([
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => 0,
                'subtotal' => 0,
            ]);
        }

        return $order;
    }

    private function createProduction(string $orderNumber, string $productName, float $actualOutput, string $status): Production
    {
        return Production::query()->create([
            'batch_number' => 'BATCH-'.fake()->unique()->numerify('####'),
            'customer_order_no' => $orderNumber,
            'product_name' => $productName,
            'planned_output' => 100,
            'actual_output' => $actualOutput,
            'unit' => 'pcs',
            'status' => $status,
            'start_date' => '2026-10-10',
        ]);
    }
}

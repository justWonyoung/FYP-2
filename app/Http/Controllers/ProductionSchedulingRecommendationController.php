<?php

namespace App\Http\Controllers;

use App\Services\ProductionSchedulingService;
use Illuminate\Contracts\View\View;

class ProductionSchedulingRecommendationController extends Controller
{
    public function index(ProductionSchedulingService $productionSchedulingService): View
    {
        $recommendation = $productionSchedulingService->recommend();
        $dailyCapacity = (float) ($recommendation['summary']['daily_capacity'] ?? 0);

        $recommendation['dailyPlans'] = collect($recommendation['recommendations'])
            ->groupBy('recommended_date')
            ->map(function ($items) use ($dailyCapacity): array {
                $capacityUsed = (float) $items->first()['daily_capacity_used'];

                return [
                    'date' => $items->first()['recommended_date'],
                    'recommendations' => $items->values()->all(),
                    'capacity_used' => $capacityUsed,
                    'capacity' => $dailyCapacity,
                    'utilization' => $dailyCapacity > 0 ? min(100, ($capacityUsed / $dailyCapacity) * 100) : 0,
                ];
            })
            ->values()
            ->all();

        return view('production.schedule-recommendations', $recommendation);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Material;
use App\Models\PurchaseRequest;
use App\Models\Production;
use App\Models\Expense;
use App\Models\CustomerOrder;


class ReportController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | ADMIN REPORT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function index()
    {


        $totalMaterials = Material::count();



        $lowStockItems = Material::whereColumn(
            'current_stock',
            '<=',
            'minimum_stock'
        )
        ->count();




        $totalPurchaseRequests = PurchaseRequest::count();



        $approvedPurchaseRequests = PurchaseRequest::where(
            'approval_status',
            'approved'
        )
        ->count();





        $totalProduction = Production::count();



        $completedProduction = Production::where(
            'status',
            'completed'
        )
        ->count();





        $totalExpense = Expense::sum(
            'amount'
        );

        $orderSummary = CustomerOrder::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $totalOrders = $orderSummary->sum();
        $completedOrderRevenue = CustomerOrder::where('status', 'completed')->sum('total_amount');
        $pendingPurchaseRequests = PurchaseRequest::where('approval_status', 'pending')->count();
        $rejectedPurchaseRequests = PurchaseRequest::where('approval_status', 'rejected')->count();
        $inProgressProduction = Production::where('status', 'in_progress')->count();
        $productionWithYield = Production::whereHas('yield')->count();
        $netProfit = $completedOrderRevenue - $totalExpense;
        $purchaseSummary = PurchaseRequest::query()
            ->selectRaw('approval_status, COUNT(*) as total')
            ->groupBy('approval_status')
            ->pluck('total', 'approval_status');
        $productionSummary = Production::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');





        return view(
            'admin.reports.index',
            compact(
                'totalMaterials',
                'lowStockItems',
                'totalPurchaseRequests',
                'approvedPurchaseRequests',
                'totalProduction',
                'completedProduction',
                'totalExpense'
                , 'totalOrders'
                , 'orderSummary'
                , 'completedOrderRevenue'
                , 'pendingPurchaseRequests'
                , 'rejectedPurchaseRequests'
                , 'inProgressProduction'
                , 'productionWithYield'
                , 'netProfit'
                , 'purchaseSummary'
                , 'productionSummary'
            )
        );


    }









    /*
    |--------------------------------------------------------------------------
    | INVENTORY REPORT
    |--------------------------------------------------------------------------
    */

    public function inventory()
    {


        $materials = Material::orderBy(
            'material_name'
        )
        ->get();



        return view(
            'admin.reports.inventory',
            compact(
                'materials'
            )
        );


    }









    /*
    |--------------------------------------------------------------------------
    | PURCHASE REPORT
    |--------------------------------------------------------------------------
    */

    public function purchase()
    {


        $purchaseRequests = PurchaseRequest::orderBy(
            'created_at',
            'desc'
        )
        ->get();




        return view(
            'admin.reports.purchase',
            compact(
                'purchaseRequests'
            )
        );


    }









    /*
    |--------------------------------------------------------------------------
    | PRODUCTION REPORT
    |--------------------------------------------------------------------------
    */

    public function production()
    {


        $productions = Production::with([

            'wastes',

            'materialUsages.material',

            'yield'

        ])
        ->orderBy(
            'created_at',
            'desc'
        )
        ->get();




        return view(
            'admin.reports.production',
            compact(
                'productions'
            )
        );


    }









    /*
    |--------------------------------------------------------------------------
    | FINANCIAL REPORT
    |--------------------------------------------------------------------------
    */

    public function financial()
    {



        $expenses = Expense::orderBy(
            'expense_date',
            'desc'
        )
        ->get();





        $totalExpense = Expense::sum(
            'amount'
        );





        return view(
            'admin.reports.financial',
            compact(
                'expenses',
                'totalExpense'
            )
        );


    }



}

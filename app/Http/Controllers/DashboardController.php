<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Expense;
use App\Models\CustomerOrder;
use App\Models\Production;


class DashboardController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function admin()
    {

        /*
        |--------------------------------------------------------------------------
        | PURCHASE REQUEST APPROVAL
        |--------------------------------------------------------------------------
        */

        $pendingApprovals = PurchaseRequest::where(
            'finance_status',
            'approved'
        )
        ->where(
            'approval_status',
            'pending'
        )
        ->get();



        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */

        $totalMaterials = Material::count();



        $lowStockItems = Material::whereColumn(
            'current_stock',
            '<=',
            'minimum_stock'
        )
        ->count();



        $criticalItems = Material::where(
            'current_stock',
            '<=',
            0
        )
        ->count();





        /*
        |--------------------------------------------------------------------------
        | FINANCE
        |--------------------------------------------------------------------------
        */

        $monthlyExpense = Expense::whereMonth(
            'expense_date',
            now()->month
        )
        ->whereYear(
            'expense_date',
            now()->year
        )
        ->sum('amount');





        /*
        |--------------------------------------------------------------------------
        | PRODUCTION
        |--------------------------------------------------------------------------
        */

        $completedProduction = Production::where(
            'status',
            'Completed'
        )
        ->count();



        $runningProduction = Production::where(
            'status',
            'In Progress'
        )
        ->count();



        $pendingProduction = Production::where(
            'status',
            'Pending'
        )
        ->count();





        /*
        |--------------------------------------------------------------------------
        | ACTIVE ORDERS
        |--------------------------------------------------------------------------
        */

        $activeOrders = Production::whereIn(
            'status',
            [
                'Pending',
                'In Progress'
            ]
        )
        ->count();

        $orderSummary = CustomerOrder::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $purchaseSummary = PurchaseRequest::query()
            ->selectRaw('approval_status, COUNT(*) as total')
            ->groupBy('approval_status')
            ->pluck('total', 'approval_status');





        return view(
            'admin.dashboard',
            compact(
                'pendingApprovals',
                'totalMaterials',
                'activeOrders',
                'monthlyExpense',
                'completedProduction',
                'runningProduction',
                'pendingProduction',
                'lowStockItems',
                'criticalItems'
                , 'orderSummary'
                , 'purchaseSummary'
            )
        );


    }









    /*
    |--------------------------------------------------------------------------
    | STAFF DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function staff()
{

    /*
    |--------------------------------------------------------------------------
    | STAFF ONLY PURCHASE REQUESTS
    |--------------------------------------------------------------------------
    */

    $requests = PurchaseRequest::with('staff')
        ->where(
            'requested_by',
            auth()->id()
        )
        ->latest()
        ->get();





    /*
    |--------------------------------------------------------------------------
    | DASHBOARD SUMMARY
    |--------------------------------------------------------------------------
    */


    $totalRequests = PurchaseRequest::where(
        'requested_by',
        auth()->id()
    )
    ->count();





    $pendingRequests = PurchaseRequest::where(
        'requested_by',
        auth()->id()
    )
    ->where(
        'approval_status',
        'pending'
    )
    ->count();





    $approvedRequests = PurchaseRequest::where(
        'requested_by',
        auth()->id()
    )
    ->where(
        'approval_status',
        'approved'
    )
    ->count();





    $rejectedRequests = PurchaseRequest::where(
        'requested_by',
        auth()->id()
    )
    ->where(
        'approval_status',
        'rejected'
    )
    ->count();







    /*
    |--------------------------------------------------------------------------
    | RECENT DATA
    |--------------------------------------------------------------------------
    */


    $pendingPRs = PurchaseRequest::where(
        'requested_by',
        auth()->id()
    )
    ->where(
        'approval_status',
        'pending'
    )
    ->get();





    $materials = Material::all();





    $productions = Production::orderBy(
        'created_at',
        'desc'
    )
    ->take(5)
    ->get();







    return view(
        'staff.dashboard',
        compact(
            'requests',
            'pendingPRs',
            'materials',
            'productions',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests'
        )
    );


}









    /*
    |--------------------------------------------------------------------------
    | FINANCE DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function finance()
    {


        $pendingReviews = PurchaseRequest::where(
            'finance_status',
            'pending'
        )
        ->get();





        $totalRevenue = CustomerOrder::query()
            ->where('status', 'completed')
            ->sum('total_amount');





        $totalExpense = Expense::sum(
            'amount'
        );





        $approvedPurchaseCosts =
            PurchaseRequest::where(
                'approval_status',
                'approved'
            )
            ->sum(
                'estimated_cost'
            );





        $netProfit = $totalRevenue - $totalExpense;

        $expenseCategorySummary = Expense::query()
            ->selectRaw('expense_category, SUM(amount) as total')
            ->groupBy('expense_category')
            ->pluck('total', 'expense_category');
        $purchaseStatusSummary = PurchaseRequest::query()
            ->selectRaw('finance_status, COUNT(*) as total')
            ->groupBy('finance_status')
            ->pluck('total', 'finance_status');





        $expenses = Expense::orderBy(
            'expense_date',
            'desc'
        )
        ->take(5)
        ->get();





        return view(
            'finance.dashboard',
            compact(
                'pendingReviews',
                'totalRevenue',
                'totalExpense',
                'approvedPurchaseCosts',
                'netProfit',
                'expenses'
                , 'expenseCategorySummary'
                , 'purchaseStatusSummary'
            )
        );


    }


}

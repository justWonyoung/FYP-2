<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Expense;
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
    |
    | Currently production is your operational order source.
    |
    */

    $activeOrders = Production::whereIn(
        'status',
        [
            'Pending',
            'In Progress'
        ]
    )
    ->count();





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

        $requests = PurchaseRequest::latest()->get();


        $pendingPRs = PurchaseRequest::where(
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
                'productions'
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



        $totalRevenue = 87000;



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



        $netProfit =
            $totalRevenue
            -
            $totalExpense
            -
            $approvedPurchaseCosts;



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
            )
        );


    }


}
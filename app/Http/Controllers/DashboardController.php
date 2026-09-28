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

        // Purchase requests waiting for Admin approval
        $pendingApprovals = PurchaseRequest::where(
            'finance_status',
            'approved'
        )
        ->where(
            'approval_status',
            'pending'
        )
        ->get();



        // Total inventory items
        $totalMaterials = Material::count();



        // Total expenses
        $totalExpenses = Expense::sum('amount');



        // Active production
        $activeOrders = Production::where(
            'status',
            'In Progress'
        )
        ->count();



        return view(
            'admin.dashboard',
            compact(
                'pendingApprovals',
                'totalMaterials',
                'totalExpenses',
                'activeOrders'
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
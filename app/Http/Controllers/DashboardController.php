<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Material;
use App\Models\Expense;
use App\Models\Production;

class DashboardController extends Controller
{
    public function admin()
    {
        $pendingPRs = PurchaseRequest::where('approval_status', 'pending')->get();
        $totalMaterials = Material::count();
        $totalExpenses = Expense::sum('amount');
        $activeOrders = Production::where('status', 'In Progress')->count();

        return view('admin.dashboard', compact('pendingPRs', 'totalMaterials', 'totalExpenses', 'activeOrders'));
    }

    public function staff()
    {
        $requests = PurchaseRequest::all();
        $pendingPRs = PurchaseRequest::where('approval_status', 'pending')->get();
        $materials = Material::all();
        $productions = Production::orderBy('created_at', 'desc')->take(5)->get();

        return view('staff.dashboard', compact('requests', 'pendingPRs', 'materials', 'productions'));
    }

    public function finance()
    {
        $pendingReviews = PurchaseRequest::where('finance_status', 'pending')->get();
        $totalRevenue = 87000;
        $totalExpense = Expense::sum('amount');
        $approvedPurchaseCosts = PurchaseRequest::where('approval_status', 'approved')->sum('estimated_cost');
        $netProfit = $totalRevenue - $totalExpense - $approvedPurchaseCosts;
        $expenses = Expense::orderBy('expense_date', 'desc')->take(5)->get();

        return view('finance.dashboard', compact('pendingReviews', 'totalRevenue', 'totalExpense', 'approvedPurchaseCosts', 'netProfit', 'expenses'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\PurchaseRequest;

class FinancialController extends Controller
{
    // Expense List & Form
    public function index()
    {
        $expenses = Expense::latest()->get();
        return view('finance.expenses', compact('expenses'));
    }

    // Store New Expense
    public function storeExpense(Request $request)
    {
        $request->validate([
            'expense_category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'expense_category' => $request->expense_category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
        ]);

        return redirect()->route('finance.expenses.index')->with('success', 'Expense record added successfully!');
    }

    // Generate Financial Report
    public function report()
    {
        $totalExpenses = Expense::sum('amount');
        $totalPurchases = PurchaseRequest::where('approval_status', 'approved')->sum('estimated_cost');
        $grandTotalCost = $totalExpenses + $totalPurchases;
        
        // Mock revenue for demonstration calculation
        $totalRevenue = 87000.00;
        $netProfit = $totalRevenue - $grandTotalCost;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        $expensesByCategory = Expense::selectRaw('expense_category, sum(amount) as total')
            ->groupBy('expense_category')
            ->pluck('total', 'expense_category');

        return view('finance.report', compact(
            'totalExpenses',
            'totalPurchases',
            'grandTotalCost',
            'totalRevenue',
            'netProfit',
            'profitMargin',
            'expensesByCategory'
        ));
    }
}
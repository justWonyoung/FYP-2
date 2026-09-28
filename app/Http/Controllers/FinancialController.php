<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\PurchaseRequest;
use App\Models\Production;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{

    /**
     * Finance Dashboard
     */
    public function dashboard()
    {
        $totalExpenses = Expense::sum('amount');

        $approvedPR = PurchaseRequest::where('approval_status', 'approved')
            ->count();

        $totalProduction = Production::count();


        return view('finance.dashboard', compact(
            'totalExpenses',
            'approvedPR',
            'totalProduction'
        ));
    }



    /**
     * Display Expense Page
     */
    public function expenses()
    {
        $expenses = Expense::latest()->get();

        return view('finance.expenses', compact('expenses'));
    }



    /**
     * Store Expense
     */
    public function storeExpense(Request $request)
    {

        $request->validate([
            'expense_category' => 'required',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'description' => 'nullable|string'
        ]);



        Expense::create([

            'expense_category' => $request->expense_category,

            'amount' => $request->amount,

            'expense_date' => $request->expense_date,

            'description' => $request->description,

            'created_by' => Auth::id()

        ]);



        return redirect()
            ->route('finance.expenses')
            ->with(
                'success',
                'Expense added successfully'
            );
    }



    /**
     * Financial Report
     */
    public function report()
    {

        $totalExpense = Expense::sum('amount');


        $purchaseCost = PurchaseRequest::where(
            'approval_status',
            'approved'
        )
        ->sum('estimated_cost');



        return view(
            'finance.report',
            compact(
                'totalExpense',
                'purchaseCost'
            )
        );

    }


}
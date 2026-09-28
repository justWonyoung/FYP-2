<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\PurchaseRequest;
use App\Models\Production;


class FinancialController extends Controller
{


    public function expenses()
    {

        $expenses = Expense::latest()->get();


        return view(
            'finance.expenses',
            compact('expenses')
        );

    }





    public function storeExpense(Request $request)
    {

        $request->validate([

            'expense_category'=>'required',

            'amount'=>'required|numeric',

            'expense_date'=>'required|date',

            'description'=>'nullable|string'

        ]);



        Expense::create([

            'expense_category'=>$request->expense_category,

            'amount'=>$request->amount,

            'expense_date'=>$request->expense_date,

            'description'=>$request->description,

        ]);



        return redirect()

            ->route('finance.expenses')

            ->with(
                'success',
                'Expense added successfully'
            );

    }






    public function report()
{

    // Total Revenue (temporary value)
    // Later can be connected with customer orders
    $totalRevenue = 87000;



    // Total Expenses

    $totalExpenses = Expense::sum('amount');



    // Approved Purchase Cost

    $totalPurchases = PurchaseRequest::where(
        'approval_status',
        'approved'
    )
    ->sum('estimated_cost');



    // Net Profit

    $netProfit =
        $totalRevenue
        -
        $totalExpenses
        -
        $totalPurchases;



    // Profit Margin

    $profitMargin = 0;


    if($totalRevenue > 0)
    {
        $profitMargin =
            ($netProfit / $totalRevenue) * 100;
    }




    // Expense Breakdown

    $expensesByCategory = Expense::selectRaw(
        'expense_category, SUM(amount) as total'
    )
    ->groupBy('expense_category')
    ->pluck('total','expense_category');




    // Used by percentage calculation in blade

    $grandTotalCost = $totalExpenses;




    return view(
        'finance.report',
        compact(

            'totalRevenue',

            'totalExpenses',

            'totalPurchases',

            'netProfit',

            'profitMargin',

            'expensesByCategory',

            'grandTotalCost'

        )
    );


}



}
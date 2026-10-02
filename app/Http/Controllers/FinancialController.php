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

    $totalRevenue = 87000;


    $totalExpenses = Expense::sum('amount');
    $expenseCount = Expense::count();

    $totalPurchases = PurchaseRequest::where(
        'approval_status',
        'approved'
    )
    ->sum('estimated_cost');

    $approvedPurchaseCount = PurchaseRequest::where(
    'approval_status',
    'approved'
)
->count();

    $netProfit =
        $totalRevenue
        -
        $totalExpenses
        -
        $totalPurchases;



    $profitMargin = 0;


    if($totalRevenue > 0)
    {

        $profitMargin =
        ($netProfit / $totalRevenue) * 100;

    }



    $expensesByCategory = Expense::selectRaw(
        'expense_category, SUM(amount) as total'
    )
    ->groupBy('expense_category')
    ->pluck(
        'total',
        'expense_category'
    );



    $grandTotalCost = $totalExpenses;

$recentExpenses = Expense::latest()
    ->take(10)
    ->get();

    $expenseCount = Expense::count();



    $approvedPurchaseCount = PurchaseRequest::where(
        'approval_status',
        'approved'
    )
    ->count();



    $recentExpenses = Expense::latest()
        ->take(10)
        ->get();



    return view(
    'finance.report',
    compact(

        'totalRevenue',

        'totalExpenses',

        'totalPurchases',

        'netProfit',

        'profitMargin',

        'expensesByCategory',

        'grandTotalCost',

        'expenseCount',

        'approvedPurchaseCount',

        'recentExpenses'

    )
);

}



}
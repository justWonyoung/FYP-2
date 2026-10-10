<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\PurchaseRequest;
use App\Models\Production;
use App\Models\CustomerOrder;
use App\Models\ActivityLog;


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



        $expense = Expense::create([

            'expense_category'=>$request->expense_category,

            'amount'=>$request->amount,

            'expense_date'=>$request->expense_date,

            'description'=>$request->description,

        ]);

        ActivityLog::record(
            $request->user(),
            'created',
            'Finance',
            "Recorded {$expense->expense_category} expense."
        );



        return redirect()

            ->route('finance.expenses')

            ->with(
                'success',
                'Expense added successfully'
            );

    }

    public function editExpense(Expense $expense)
    {
        return view('finance.expense-edit', compact('expense'));
    }

    public function updateExpense(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_category' => 'required|string|in:Raw Materials,Packaging,Labor / Utility,Logistics',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        $expense->update($validated);

        ActivityLog::record(
            $request->user(),
            'updated',
            'Finance',
            "Updated {$expense->expense_category} expense."
        );

        return redirect()->route('finance.expenses')->with('success', 'Expense updated successfully');
    }






public function report(Request $request)
{
    $dateFrom = $request->date('date_from');
    $dateTo = $request->date('date_to');

    $withinPeriod = static function ($query, string $column) use ($dateFrom, $dateTo) {
        return $query
            ->when($dateFrom, fn ($q) => $q->whereDate($column, '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate($column, '<=', $dateTo));
    };

    $totalRevenue = CustomerOrder::query()
        ->where('status', 'completed')
        ->tap(fn ($q) => $withinPeriod($q, 'order_date'))
        ->sum('total_amount');


    $expenseQuery = Expense::query();
    $withinPeriod($expenseQuery, 'expense_date');
    $totalExpenses = (clone $expenseQuery)->sum('amount');
    $expenseCount = (clone $expenseQuery)->count();

    $purchaseQuery = PurchaseRequest::where('approval_status', 'approved');
    $withinPeriod($purchaseQuery, 'created_at');
    $totalPurchases = (clone $purchaseQuery)->sum('estimated_cost');

    $approvedPurchaseCount = (clone $purchaseQuery)->count();

    $netProfit = $totalRevenue - $totalExpenses;



    $profitMargin = 0;


    if($totalRevenue > 0)
    {

        $profitMargin =
        ($netProfit / $totalRevenue) * 100;

    }



    $expensesByCategory = (clone $expenseQuery)->selectRaw(
        'expense_category, SUM(amount) as total'
    )
    ->groupBy('expense_category')
    ->pluck(
        'total',
        'expense_category'
    );



    $grandTotalCost = $totalExpenses;

$recentExpenses = (clone $expenseQuery)->latest()
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

        'recentExpenses',
        'dateFrom',
        'dateTo'

    )
);

}



}

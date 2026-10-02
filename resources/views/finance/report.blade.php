@extends('layouts.app')


@php

$pageTitle = "Financial Report | Beauty Kasih";
$moduleTitle = "Financial Report";
$moduleSubtitle = "Profitability & Expense Analysis";

@endphp



@section('content')



<div class="d-flex justify-content-end mb-4">


<button onclick="window.print()"
class="btn btn-outline-secondary">

<i class="bi bi-printer me-1"></i>

Print / Download PDF

</button>


</div>





<div class="row g-4 mb-4">



<div class="col-md-3">

<div class="dashboard-card">

<div class="card-title">

TOTAL REVENUE (MTD)

</div>


<div class="card-value text-success">

RM {{ number_format($totalRevenue,2) }}

</div>


</div>

</div>




<div class="col-md-3">

<div class="dashboard-card">

<div class="card-title">

OPERATIONAL EXPENSES

</div>


<div class="card-value text-danger">

RM {{ number_format($totalExpenses,2) }}

</div>


</div>

</div>





<div class="col-md-3">

<div class="dashboard-card">

<div class="card-title">

APPROVED PURCHASE COSTS

</div>


<div class="card-value text-warning">

RM {{ number_format($totalPurchases,2) }}

</div>


</div>

</div>





<div class="col-md-3">

<div class="dashboard-card">

<div class="card-title">

ESTIMATED NET PROFIT

</div>


<div class="card-value text-primary">

RM {{ number_format($netProfit,2) }}

</div>


<small>

Margin:
{{ number_format($profitMargin,1) }}%

</small>


</div>

</div>



</div>







<div class="section-card">


<div class="section-title mb-4">

Expense Breakdown by Category

</div>




<table class="table align-middle">


<thead>

<tr>

<th>
CATEGORY
</th>

<th>
TOTAL AMOUNT
</th>

<th>
SHARE OF TOTAL
</th>

</tr>

</thead>



<tbody>


@forelse($expensesByCategory as $cat=>$amt)


<tr>


<td>

<strong>

{{ $cat }}

</strong>

</td>



<td>

RM {{ number_format($amt,2) }}

</td>



<td>

{{ number_format(($amt/$grandTotalCost)*100,1) }}%

</td>


</tr>



@empty


<tr>

<td colspan="3"
class="text-center text-muted py-5">

No financial expenses recorded for analysis.

</td>

</tr>


@endforelse



</tbody>


</table>



</div>



@endsection
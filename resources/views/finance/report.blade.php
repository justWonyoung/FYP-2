@extends('layouts.app')


@php

$pageTitle = "Financial Report | Beauty Kasih";

$moduleTitle = "Financial Report";

$moduleSubtitle = "Profitability & Expense Analysis";

@endphp



@section('content')



<style>


@media print {


@page {

    size:A4 portrait;

    margin:20mm;

}



body {

    background:white !important;

}



.sidebar,
.top-navbar,
.no-print,
button,
.btn {

    display:none !important;

}



.dashboard-wrapper,
.main-content,
.page-container {

    margin:0!important;

    padding:0!important;

    width:100%!important;

}



.print-report {

    width:100%!important;

    padding:10mm!important;

    border:none!important;

    box-shadow:none!important;

}



.report-card {

    break-inside:avoid;

}



.report-section {

    break-inside:avoid;

}



table {

    width:100%!important;

    font-size:11px!important;

}



th,
td {

    padding:7px!important;

}



tr {

    page-break-inside:auto;

}



}





.print-report {

background:white;

padding:35px;

border-radius:12px;

}





.report-header {

border-bottom:2px solid #ddd;

padding-bottom:15px;

margin-bottom:20px;

}





.report-company {

font-size:22px;

font-weight:800;

}





.report-title {

font-size:17px;

font-weight:700;

}





.report-date {

font-size:13px;

color:#777;

}






/* SUMMARY CARD */


.report-card {


border:1px solid #ddd;

border-radius:14px;

padding:18px;

height:100%;

background:white;

}





.report-card-title {


font-size:12px;

font-weight:700;

color:#666;

letter-spacing:.5px;

}





.report-card-value {


font-size:22px;

font-weight:800;

margin-top:8px;

white-space:nowrap;

}





.report-card small {


display:block;

margin-top:8px;

color:#888;

}





.report-section {


border:1px solid #ddd;

border-radius:12px;

padding:20px;

margin-top:25px;

page-break-inside:avoid;

break-inside:avoid;


}





.report-section-title {


font-size:18px;

font-weight:700;

margin-bottom:15px;

}





.report-footer {


margin-top:25px;

padding-top:10px;

border-top:1px solid #ddd;

text-align:center;

font-size:10px;

color:#777;

page-break-inside:avoid;

break-inside:avoid;


}

@media print {

.report-footer {

    position:relative;

    margin-top:15px!important;

}

.report-section {

    margin-top:15mm!important;

}


.report-card {

    padding:12px!important;

}


.report-footer {

    margin-top:15mm!important;

}


}

</style>






<div class="d-flex justify-content-end mb-4 no-print">


<button onclick="window.print()"

class="btn btn-outline-secondary">


<i class="bi bi-printer me-1"></i>


Print / Download PDF


</button>


</div>






<div class="print-report">





<div class="report-header">


<div class="report-company">

Beauty Kasih Management System

</div>



<div class="report-title">

Financial Performance Report

</div>



<div class="report-date">

Generated {{ now()->format('d M Y') }}

</div>


</div>






<!-- SUMMARY CARDS -->

<div class="row g-3">



<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

TOTAL REVENUE

</div>



<div class="report-card-value text-success">

RM {{ number_format($totalRevenue,2) }}

</div>



<small>

Monthly income tracking

</small>


</div>


</div>







<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

TOTAL EXPENSES

</div>



<div class="report-card-value text-danger">

RM {{ number_format($totalExpenses,2) }}

</div>



<small>

{{ $expenseCount }} expense records

</small>


</div>


</div>







<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

PURCHASE COST

</div>



<div class="report-card-value text-warning">

RM {{ number_format($totalPurchases,2) }}

</div>



<small>

{{ $approvedPurchaseCount }} approved requests

</small>


</div>


</div>







<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

NET PROFIT

</div>



<div class="report-card-value text-primary">

RM {{ number_format($netProfit,2) }}

</div>



<small>

Margin:
{{ number_format($profitMargin,2) }}%

</small>



@if($netProfit > 0)

<span class="badge bg-success mt-2">

Profitable

</span>


@else


<span class="badge bg-danger mt-2">

Loss

</span>


@endif



</div>


</div>




</div>










<!-- EXTRA SUMMARY -->


<div class="row g-3 mt-2">


<div class="col-md-6">


<div class="report-card py-3">


<div class="report-card-title">

TOTAL EXPENSE RECORDS

</div>



<div class="report-card-value">

{{ $expenseCount }}

</div>



<small>

Operational spending activities

</small>


</div>


</div>






<div class="col-md-6">


<div class="report-card py-3">


<div class="report-card-title">

APPROVED PURCHASE REQUESTS

</div>



<div class="report-card-value">

{{ $approvedPurchaseCount }}

</div>



<small>

Procurement activities completed

</small>


</div>


</div>



</div>









<!-- EXPENSE BREAKDOWN -->


<div class="report-section">


<div class="report-section-title">

Expense Breakdown By Category

</div>





<table class="table table-bordered align-middle">


<thead>


<tr>


<th>

CATEGORY

</th>


<th>

TOTAL AMOUNT

</th>


<th>

SHARE

</th>


</tr>


</thead>




<tbody>



@forelse($expensesByCategory as $category=>$amount)



<tr>


<td>


<strong>

{{ $category }}

</strong>


</td>




<td>

RM {{ number_format($amount,2) }}

</td>





<td>


@if($grandTotalCost > 0)

{{ number_format(($amount/$grandTotalCost)*100,2) }}%


@else

0%


@endif



</td>


</tr>




@empty


<tr>


<td colspan="3"

class="text-center text-muted">


No expense data available.


</td>


</tr>



@endforelse




</tbody>


</table>



</div>









<!-- RECENT EXPENSE -->


<div class="report-section">


<div class="report-section-title">

Recent Expense Transactions

</div>





<table class="table table-bordered align-middle">


<thead>


<tr>


<th>

DATE

</th>


<th>

CATEGORY

</th>


<th>

DESCRIPTION

</th>


<th>

AMOUNT

</th>


</tr>


</thead>






<tbody>




@forelse($recentExpenses as $expense)




<tr>



<td>

{{ $expense->expense_date }}

</td>




<td>

{{ $expense->expense_category }}

</td>




<td>

{{ $expense->description }}

</td>




<td>


<strong class="text-danger">

RM {{ number_format($expense->amount,2) }}

</strong>


</td>




</tr>




@empty




<tr>


<td colspan="4"

class="text-center text-muted">


No expense transactions recorded.


</td>


</tr>




@endforelse




</tbody>


</table>



</div>









<div class="report-footer">


Beauty Kasih Management System

<br>

Confidential Internal Document

<br>

Generated {{ now()->format('d M Y H:i') }}



</div>







</div>





@endsection
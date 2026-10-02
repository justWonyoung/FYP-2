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

        size: A4 portrait;

        margin: 15mm;

    }



    body {

        background: white !important;

    }



    .dashboard-wrapper {

        display:block !important;

    }



    .sidebar,
    .top-navbar,
    button,
    .btn {

        display:none !important;

    }



    .main-content {

        margin:0 !important;

        padding:0 !important;

        width:100% !important;

    }



    .page-container {

        padding:0 !important;

        margin:0 !important;

    }



    .print-report {

        width:100% !important;

        box-shadow:none !important;

        border:none !important;

    }



}





.print-report {


    background:white;

    padding:35px;

}



.report-header {


    border-bottom:2px solid #ddd;

    padding-bottom:20px;

    margin-bottom:25px;

}



.report-company {


    font-size:24px;

    font-weight:800;

}



.report-title {


    font-size:20px;

    font-weight:700;

}



.report-date {


    color:#666;

    font-size:13px;

}





.report-card {


    border:1px solid #ddd;

    border-radius:12px;

    padding:20px;

    height:100%;

}



.report-card-title {


    font-size:12px;

    font-weight:700;

    color:#666;

}



.report-card-value {


    font-size:24px;

    font-weight:800;

    margin-top:10px;

}



.report-section {


    border:1px solid #ddd;

    border-radius:12px;

    padding:25px;

    margin-top:25px;

}



.report-section-title {


    font-size:18px;

    font-weight:700;

    margin-bottom:20px;

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

Financial Report

</div>


<div class="report-date">

Generated Date:
{{ now()->format('d M Y') }}

</div>



</div>









<div class="row g-3">



<div class="col-3">


<div class="report-card">


<div class="report-card-title">

TOTAL REVENUE (MTD)

</div>


<div class="report-card-value text-success">

RM {{ number_format($totalRevenue,2) }}

</div>


</div>


</div>






<div class="col-3">


<div class="report-card">


<div class="report-card-title">

OPERATIONAL EXPENSES

</div>


<div class="report-card-value text-danger">

RM {{ number_format($totalExpenses,2) }}

</div>


</div>


</div>






<div class="col-3">


<div class="report-card">


<div class="report-card-title">

APPROVED PURCHASE COSTS

</div>


<div class="report-card-value text-warning">

RM {{ number_format($totalPurchases,2) }}

</div>


</div>


</div>






<div class="col-3">


<div class="report-card">


<div class="report-card-title">

ESTIMATED NET PROFIT

</div>


<div class="report-card-value text-primary">

RM {{ number_format($netProfit,2) }}

</div>


<small>

Margin:
{{ number_format($profitMargin,1) }}%

</small>


</div>


</div>



</div>











<div class="report-section">


<div class="report-section-title">

Expense Breakdown by Category

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


@if($grandTotalCost > 0)

{{ number_format(($amt/$grandTotalCost)*100,1) }}%

@else

0%

@endif


</td>


</tr>


@empty


<tr>

<td colspan="3"
class="text-center">

No financial expenses recorded.

</td>

</tr>


@endforelse



</tbody>


</table>




</div>





</div>





@endsection
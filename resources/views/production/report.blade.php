@extends('layouts.app')


@php

$pageTitle = "Production Report | NEXORA";

$moduleTitle = "Production Report";

$moduleSubtitle = "Production Performance & Batch Analysis";

@endphp



@section('content')


<style>


@media print {


@page {

    size: landscape;

    margin:12mm;

}



body {

    background:white !important;

}



.sidebar,
.top-navbar,
.no-print,
button {

    display:none !important;

}



.dashboard-wrapper,
.main-content,
.page-container {

    padding:0!important;

    margin:0!important;

    width:100%!important;

}



.report-container {

    border:none!important;

    box-shadow:none!important;

    width:100%!important;

}



.company-name {

    font-size:28px!important;

}



.report-title {

    font-size:20px!important;

}



table {

    width:100%!important;

    font-size:13px!important;

}



th,
td {

    padding:10px!important;

}



tr {

    page-break-inside:avoid;

}



}



.report-container {

background:white;

padding:20px;

border-radius:12px;

}



.report-header {

border-bottom:2px solid #ddd;

padding-bottom:12px;

margin-bottom:20px;

}



.company-name {

font-size:28px;

font-weight:800;

}



.report-title {

font-size:20px;

font-weight:700;

}



.report-date {

color:#777;

font-size:13px;

}




.summary-card {

border:1px solid #ddd;

border-radius:12px;

padding:15px;

height:100%;

}



.summary-title {

font-size:12px;

font-weight:700;

color:#666;

}



.summary-value {

font-size:25px;

font-weight:800;

margin-top:5px;

}



.section-box {

border:1px solid #ddd;

border-radius:12px;

padding:20px;

margin-top:25px;

}



.section-title {

font-size:18px;

font-weight:700;

margin-bottom:15px;

}



.report-footer {

margin-top:60px;

padding-top:15px;

border-top:1px solid #ddd;

text-align:center;

font-size:11px;

color:#777;

}


</style>





<!-- ================= PRINT BUTTON ================= -->

<div class="d-flex justify-content-end mb-4 no-print">


    <button onclick="window.print()"
            class="btn btn-outline-secondary px-4">


        <i class="bi bi-printer me-1"></i>

        Print / Download PDF


    </button>


</div>






<div class="report-container">



<div class="report-header">


<div class="company-name">

NEXORA Management System

</div>


<div class="report-title">

Production Performance Report

</div>


<div class="report-date">

Generated {{ now()->format('d M Y') }}

</div>


</div>







<div class="row g-4 mb-4">


<div class="col-md-3">

<div class="summary-card">


<div class="summary-title">

TOTAL BATCHES

</div>


<div class="summary-value">

{{ $totalBatches }}

</div>


</div>

</div>





<div class="col-md-3">

<div class="summary-card">


<div class="summary-title">

COMPLETED

</div>


<div class="summary-value text-success">

{{ $completedBatches }}

</div>


</div>

</div>





<div class="col-md-3">

<div class="summary-card">


<div class="summary-title">

IN PROGRESS

</div>


<div class="summary-value text-warning">

{{ $inProgress }}

</div>


</div>

</div>





<div class="col-md-3">

<div class="summary-card">


<div class="summary-title">

YIELD %

</div>


<div class="summary-value text-primary">

{{ number_format($yield,2) }}%

</div>


</div>

</div>



</div>







<div class="section-box">


<div class="section-title">

Production Batch Analysis

</div>





<table class="table table-bordered align-middle">


<thead>


<tr>

<th>
BATCH NO
</th>


<th>
PRODUCT
</th>


<th>
ORDER NO
</th>


<th>
PLANNED OUTPUT
</th>


<th>
ACTUAL OUTPUT
</th>


<th>
STATUS
</th>


</tr>


</thead>




<tbody>



@forelse($batches as $batch)


<tr>


<td>

<strong>

{{ $batch->batch_number }}

</strong>

</td>



<td>

{{ $batch->product_name }}

</td>



<td>

{{ $batch->customer_order_no }}

</td>



<td>

{{ number_format($batch->planned_output,2) }}

{{ $batch->unit }}

</td>



<td>

{{ number_format($batch->actual_output,2) }}

{{ $batch->unit }}

</td>




<td>


@if($batch->status == 'completed')


<span class="badge bg-success">

Completed

</span>


@else


<span class="badge bg-warning text-dark">

In Progress

</span>


@endif



</td>


</tr>


@empty


<tr>

<td colspan="6"
class="text-center">

No production records found.

</td>

</tr>


@endforelse



</tbody>



</table>



</div>






<div class="report-footer">


NEXORA Digital Management System

<br>

Confidential Internal Document

<br>

Generated {{ now()->format('d M Y H:i') }}


</div>




</div>




@endsection
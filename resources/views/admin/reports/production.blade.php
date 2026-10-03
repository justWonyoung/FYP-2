@extends('layouts.app')


@php

$pageTitle = "Production Report | DBFM";

$moduleTitle = "Production Report";

$moduleSubtitle = "Manufacturing Performance Analysis";

@endphp



@section('content')


<style>

@media print {


@page {

    size: A4 landscape;

    margin: 10mm;

}



body {

    background:white !important;

    -webkit-print-color-adjust: exact !important;

    print-color-adjust: exact !important;

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

    box-shadow:none !important;

    border:none !important;

    padding:15px !important;

}



.section-box {

    page-break-inside: avoid;

}



table {

    page-break-inside:auto;

}



tr {

    page-break-inside:avoid;

    page-break-after:auto;

}



.report-footer {

    page-break-before:avoid;

}



}



.print-report {

background:white;

padding:20px;

}



.report-header {

border-bottom:2px solid #ddd;

padding-bottom:20px;

margin-bottom:25px;

}



.company-name {

font-size:24px;

font-weight:800;

}



.report-title {

font-size:20px;

font-weight:700;

}



.report-date {

font-size:13px;

color:#666;

}





.summary-card {

border:1px solid #ddd;

border-radius:12px;

padding:18px;

height:100%;

}



.summary-title {

font-size:12px;

font-weight:700;

color:#666;

}



.summary-value {

font-size:20px;

font-weight:800;

margin-top:10px;

}

.table {

font-size:12px;

}

.section-box {

border:1px solid #ddd;

border-radius:12px;

padding:18px;

margin-top:15px;

}



.section-title {

font-size:18px;

font-weight:700;

margin-bottom:20px;

}



.report-footer {

margin-top:50px;

border-top:1px solid #ddd;

padding-top:15px;

text-align:center;

font-size:11px;

color:#777;

}



</style>





<div class="d-flex justify-content-end mb-4">


<button onclick="window.print()"
class="btn btn-outline-secondary">


<i class="bi bi-printer me-1"></i>

Print / Download PDF


</button>


</div>







<div class="print-report">



<div class="report-header">


<div class="company-name">

DBFM DIGITAL MANAGEMENT SYSTEM

</div>


<div class="report-title">

Production Performance Report

</div>


<div class="report-date">

Generated Date:
{{ now()->format('d M Y') }}

</div>


</div>







@php


$totalBatches = $productions->count();


$totalPlanned = $productions->sum('planned_output');


$totalActual = $productions->sum('actual_output');


$completed = $productions
->where('status','completed')
->count();



$achievement = 0;


if($totalPlanned > 0)
{

$achievement = ($totalActual/$totalPlanned)*100;

}



$completionRate = 0;


if($totalBatches > 0)
{

$completionRate =
($completed/$totalBatches)*100;

}



@endphp







<div class="row g-3">



<div class="col-3">

<div class="summary-card">


<div class="summary-title">

TOTAL BATCHES

</div>


<div class="summary-value">

{{ $totalBatches }}

</div>


</div>

</div>





<div class="col-3">

<div class="summary-card">


<div class="summary-title">

OUTPUT ACHIEVEMENT

</div>


<div class="summary-value text-primary">

{{ number_format($achievement,1) }}%

</div>


</div>

</div>





<div class="col-3">

<div class="summary-card">


<div class="summary-title">

COMPLETED RATE

</div>


<div class="summary-value text-success">

{{ number_format($completionRate,1) }}%

</div>


</div>

</div>






<div class="col-3">

<div class="summary-card">


<div class="summary-title">

ACTUAL OUTPUT

</div>


<div class="summary-value">

{{ number_format($totalActual,2) }}

</div>


</div>

</div>




</div>









<!-- BATCH REPORT -->


<div class="section-box">


<div class="section-title">

Production Batch Analysis

</div>



<table class="table table-bordered align-middle">


<thead>


<tr>


<th>
BATCH
</th>


<th>
PRODUCT
</th>


<th>
PLANNED
</th>


<th>
ACTUAL
</th>


<th>
ACHIEVEMENT
</th>


<th>
STATUS
</th>


</tr>


</thead>




<tbody>


@forelse($productions as $production)



<tr>


<td>

<strong>

{{ $production->batch_number }}

</strong>

</td>



<td>

{{ $production->product_name }}

</td>



<td>

{{ number_format($production->planned_output,2) }}

{{ $production->unit }}

</td>



<td>

{{ number_format($production->actual_output,2) }}

{{ $production->unit }}

</td>



<td>


@if($production->planned_output > 0)


{{ number_format(($production->actual_output/$production->planned_output)*100,1) }}%


@else

-

@endif


</td>



<td>

{{ ucfirst(str_replace('_',' ',$production->status)) }}

</td>



</tr>


@empty


<tr>

<td colspan="6"
class="text-center">

No production records.

</td>

</tr>


@endforelse



</tbody>


</table>


</div>









<!-- MATERIAL USAGE -->


<div class="section-box">


<div class="section-title">

Material Consumption Analysis

</div>




<table class="table table-bordered">


<thead>


<tr>


<th>
BATCH
</th>


<th>
MATERIAL
</th>


<th>
QUANTITY USED
</th>


</tr>


</thead>



<tbody>



@foreach($productions as $production)


@foreach($production->materialUsages as $usage)



<tr>


<td>

{{ $production->batch_number }}

</td>


<td>

{{ $usage->material->material_name ?? '-' }}

</td>


<td>

{{ number_format($usage->quantity_used,2) }}

{{ $usage->material->unit ?? '' }}

</td>


</tr>


@endforeach


@endforeach



</tbody>


</table>



</div>









<!-- WASTE ANALYSIS -->


<div class="section-box">


<div class="section-title">

Material Waste Analysis

</div>




<table class="table table-bordered">


<thead>


<tr>


<th>
BATCH
</th>


<th>
MATERIAL
</th>


<th>
WASTE QUANTITY
</th>


<th>
REASON
</th>


</tr>


</thead>



<tbody>



@foreach($productions as $production)


@foreach($production->wastes as $waste)



<tr>


<td>

{{ $production->batch_number }}

</td>


<td>

{{ $waste->material->material_name ?? '-' }}

</td>


<td>

{{ number_format($waste->quantity_wasted,2) }}

</td>


<td>

{{ $waste->waste_reason }}

</td>


</tr>


@endforeach


@endforeach



</tbody>


</table>


</div>









<!-- YIELD -->


<div class="section-box">


<div class="section-title">

Production Yield Performance

</div>




<table class="table table-bordered">


<thead>


<tr>


<th>
BATCH
</th>


<th>
PLANNED QTY
</th>


<th>
ACTUAL QTY
</th>


<th>
YIELD %

</th>


</tr>


</thead>



<tbody>



@foreach($productions as $production)


@if($production->yield)



<tr>


<td>

{{ $production->batch_number }}

</td>


<td>

{{ number_format($production->yield->planned_quantity,2) }}

</td>


<td>

{{ number_format($production->yield->actual_quantity,2) }}

</td>


<td>

{{ number_format($production->yield->yield_percentage,2) }}%

</td>


</tr>



@endif


@endforeach



</tbody>


</table>



</div>







<div class="report-footer">


DBFM DIGITAL MANAGEMENT SYSTEM

<br>

Confidential Internal Document

<br>

Generated {{ now()->format('d M Y H:i') }}


</div>






</div>




@endsection
@extends('layouts.app')


@php

$pageTitle = "Inventory Report | NEXORA";

$moduleTitle = "Inventory Report";

$moduleSubtitle = "Stock Analysis & Material Availability";

@endphp



@section('content')



<style>

@media print {


@page {

    size: landscape;

    margin: 12mm;

}



body {

    background:white !important;

    font-family: Arial, sans-serif;

}



/* REMOVE SYSTEM UI */

.sidebar,
.top-navbar,
.no-print,
button {

    display:none !important;

}



.dashboard-wrapper,
.main-content,
.page-container {

    margin:0 !important;

    padding:0 !important;

    width:100% !important;

}



.report-container {

    width:100% !important;

    padding:18px !important;

    margin:0 !important;

    border:none !important;

    box-shadow:none !important;

}



/* HEADER */

.report-header {

    padding-bottom:12px !important;

    margin-bottom:18px !important;

}



.company-name {

    font-size:26px !important;

}



.report-title {

    font-size:20px !important;

}



.report-date {

    font-size:12px !important;

}



/* SUMMARY CARDS */


.row {

    --bs-gutter-x:18px !important;

    --bs-gutter-y:10px !important;

}



.summary-card {

    padding:14px !important;

    border-radius:10px !important;

}



.summary-title {

    font-size:11px !important;

}



.summary-value {

    font-size:24px !important;

    margin-top:6px !important;

}



/* TABLE SECTION */


.section-box {

    padding:14px !important;

    margin-top:18px !important;

    border-radius:10px !important;

}



.section-title {

    font-size:17px !important;

    margin-bottom:12px !important;

}



table {

    width:100% !important;

    font-size:12px !important;

    margin-bottom:0 !important;

}



th {

    padding:8px !important;

    font-size:11px !important;

}



td {

    padding:8px !important;

}



tr {

    page-break-inside:avoid !important;

}



thead {

    display:table-header-group;

}



.badge {

    font-size:10px !important;

    padding:4px 8px !important;

}



/* FOOTER */


.report-footer {

    margin-top:25px !important;

    padding-top:10px !important;

    font-size:10px !important;

}



}



/* NORMAL SCREEN */


.report-container {

    background:white;

    padding:15px;

    border-radius:12px;

}



.report-header {

    border-bottom:2px solid #ddd;

    padding-bottom:10px;

    margin-bottom:15px;

}



.company-name {

    font-size:26px;

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

    padding:12px;

    height:100%;

}



.summary-title {

    font-size:12px;

    color:#666;

    font-weight:700;

}



.summary-value {

    font-size:22px;

    font-weight:800;

    margin-top:5px;

}



.section-box {

    border:1px solid #ddd;

    border-radius:12px;

    padding:25px;

    margin-top:25px;

}



.section-title {

    font-size:18px;

    font-weight:700;

    margin-bottom:20px;

}



.report-footer {

    margin-top:40px;

    border-top:1px solid #ddd;

    padding-top:15px;

    text-align:center;

    font-size:11px;

    color:#777;

}


</style>





<div class="d-flex justify-content-end mb-4 no-print">


<button onclick="window.print()"
class="btn btn-outline-secondary">


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

Inventory Stock Report

</div>



<div class="report-date">

Generated {{ now()->format('d M Y') }}

</div>



</div>







<div class="row g-4 mb-4">



<div class="col-md-4">


<div class="summary-card">


<div class="summary-title">

TOTAL MATERIALS

</div>


<div class="summary-value">

{{ $totalMaterials }}

</div>


</div>


</div>







<div class="col-md-4">


<div class="summary-card">


<div class="summary-title">

LOW STOCK ITEMS

</div>


<div class="summary-value text-danger">

{{ $lowStock }}

</div>


</div>


</div>







<div class="col-md-4">


<div class="summary-card">


<div class="summary-title">

TOTAL STOCK QUANTITY

</div>


<div class="summary-value text-primary">

{{ number_format($totalStock,2) }}

</div>


</div>


</div>



</div>








<div class="section-box">



<div class="section-title">

Material Stock Analysis

</div>





<table class="table table-bordered align-middle">


<thead>


<tr>

<th>
MATERIAL
</th>


<th>
TYPE
</th>


<th>
CURRENT STOCK
</th>


<th>
MINIMUM STOCK
</th>


<th>
STATUS
</th>


</tr>


</thead>





<tbody>



@forelse($materials as $item)


<tr>


<td>

<strong>

{{ $item->material_name }}

</strong>

</td>




<td>

{{ $item->material_type }}

</td>





<td>

{{ number_format($item->current_stock,2) }}

{{ $item->unit }}

</td>





<td>

{{ number_format($item->minimum_stock,2) }}

{{ $item->unit }}

</td>





<td>


@if($item->current_stock <= $item->minimum_stock)


<span class="badge bg-warning text-dark">

Low Stock

</span>



@else


<span class="badge bg-success">

Available

</span>



@endif



</td>



</tr>



@empty


<tr>


<td colspan="5"
class="text-center">


No material records found.


</td>


</tr>


@endforelse



</tbody>



</table>




</div>







<div class="report-footer">


NEXORA Management System

<br>

Confidential Internal Document

<br>

Generated {{ now()->format('d M Y H:i') }}


</div>





</div>




@endsection
@extends('layouts.app')


@php

$pageTitle = "Analytics | NEXORA";

$moduleTitle = "Analytics";

$moduleSubtitle = "Operational Summary & Business Performance";

@endphp



@section('content')





<div class="page-header">


<h1>

Business Analytics

</h1>


<p>

Management overview and operational performance summary.

</p>


</div>









<div class="row g-4">





<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="card-title">

Inventory Items

</div>



<div class="card-value">

{{ $totalMaterials }}

</div>



<div class="stat-change">

{{ $lowStockItems }} Low Stock Items

</div>



</div>


</div>









<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="card-title">

Purchase Requests

</div>



<div class="card-value">

{{ $totalPurchaseRequests }}

</div>



<div class="stat-change">

{{ $approvedPurchaseRequests }} Approved

</div>



</div>


</div>









<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="card-title">

Production Batches

</div>



<div class="card-value">

{{ $totalProduction }}

</div>



<div class="stat-change">

{{ $completedProduction }} Completed

</div>



</div>


</div>









<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="card-title">

Operating Cost

</div>



<div class="card-value">

RM {{ number_format($totalExpense,2) }}

</div>



<div class="stat-change">

Recorded Expenses

</div>



</div>


</div>





</div>













<div class="section-card mt-4">



<div class="section-title">

Business Overview

</div>



<div class="section-subtitle mb-4">

Current operational status across all departments.

</div>







<div class="row g-4">







<div class="col-md-3">


<div class="dashboard-card">



<div class="card-title">

Inventory Status

</div>



<div class="card-value">

{{ $totalMaterials }}

</div>



<small>

Material Items

</small>



</div>


</div>









<div class="col-md-3">


<div class="dashboard-card">



<div class="card-title">

Purchase Activity

</div>



<div class="card-value">

{{ $totalPurchaseRequests }}

</div>



<small>

Requests Recorded

</small>



</div>


</div>









<div class="col-md-3">


<div class="dashboard-card">



<div class="card-title">

Production Activity

</div>



<div class="card-value">

{{ $totalProduction }}

</div>



<small>

Production Batches

</small>



</div>


</div>









<div class="col-md-3">


<div class="dashboard-card">



<div class="card-title">

Operating Cost

</div>



<div class="card-value">

RM {{ number_format($totalExpense,2) }}

</div>



<small>

Total Expenses

</small>



</div>


</div>





</div>





</div>







@endsection
@extends('layouts.app')


@php

$pageTitle = "Reports | Beauty Kasih";

$moduleTitle = "Reports";

$moduleSubtitle = "Operational Summary & Analysis";

@endphp



@section('content')



<div class="page-header">


<h1>
Admin Reports
</h1>


<p>
Operational monitoring and business performance summary.
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
Total Expenses
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

Report Categories

</div>


<div class="section-subtitle mb-4">

Detailed operational reports

</div>





<div class="row g-4">



<div class="col-md-3">

<a href="{{ route('admin.reports.inventory') }}"
class="btn btn-outline-primary w-100">

Inventory Report

</a>

</div>





<div class="col-md-3">

<a href="{{ route('admin.reports.purchase') }}"
class="btn btn-outline-primary w-100">

Purchase Report

</a>

</div>





<div class="col-md-3">

<a href="{{ route('admin.reports.production') }}"
class="btn btn-outline-primary w-100">

Production Report

</a>

</div>





<div class="col-md-3">

<a href="{{ route('admin.reports.financial') }}"
class="btn btn-outline-primary w-100">

Financial Report

</a>

</div>



</div>


</div>



@endsection
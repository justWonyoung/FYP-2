@extends('layouts.app')


@php

$pageTitle = "Analytics | DBFM";

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

<div class="row g-4">
    <div class="col-lg-4"><div class="section-card h-100 mb-0"><div class="section-title mb-3">Customer Order Status</div><div style="height:230px"><canvas id="orderStatusChart"></canvas></div></div></div>
    <div class="col-lg-4"><div class="section-card h-100 mb-0"><div class="section-title mb-3">Purchase Request Status</div><div style="height:230px"><canvas id="purchaseStatusChart"></canvas></div></div></div>
    <div class="col-lg-4"><div class="section-card h-100 mb-0"><div class="section-title mb-3">Production Status</div><div style="height:230px"><canvas id="productionStatusChart"></canvas></div></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const chartOptions = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } };
new Chart(document.getElementById('orderStatusChart'), { type:'doughnut', data:{ labels:@json($orderSummary->keys()), datasets:[{ data:@json($orderSummary->values()), backgroundColor:['#f59e0b','#7c3aed','#16a34a','#dc2626'] }] }, options:chartOptions });
new Chart(document.getElementById('purchaseStatusChart'), { type:'doughnut', data:{ labels:@json($purchaseSummary->keys()), datasets:[{ data:@json($purchaseSummary->values()), backgroundColor:['#f59e0b','#16a34a','#dc2626','#64748b'] }] }, options:chartOptions });
new Chart(document.getElementById('productionStatusChart'), { type:'doughnut', data:{ labels:@json($productionSummary->keys()), datasets:[{ data:@json($productionSummary->values()), backgroundColor:['#7c3aed','#16a34a','#f59e0b','#64748b'] }] }, options:chartOptions });
</script>

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







<div class="section-card mt-4">
<div class="section-title">Operational &amp; Financial Summary</div>
<div class="section-subtitle mb-4">Read-only metrics calculated from current DBFM records.</div>
<div class="row g-4">
<div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-title">Customer Orders</div><div class="card-value">{{ $totalOrders }}</div><small>Pending {{ $orderSummary->get('pending', 0) }} · Production {{ $orderSummary->get('production', 0) }}</small><br><small>Completed {{ $orderSummary->get('completed', 0) }} · Cancelled {{ $orderSummary->get('cancelled', 0) }}</small></div></div>
<div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-title">Completed Revenue</div><div class="card-value">RM {{ number_format($completedOrderRevenue, 2) }}</div><small>Completed customer orders</small></div></div>
<div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-title">Purchase Requests</div><div class="card-value">{{ $totalPurchaseRequests }}</div><small>Pending {{ $pendingPurchaseRequests }} · Approved {{ $approvedPurchaseRequests }} · Rejected {{ $rejectedPurchaseRequests }}</small></div></div>
<div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-title">Production</div><div class="card-value">{{ $totalProduction }}</div><small>In progress {{ $inProgressProduction }} · Completed {{ $completedProduction }}</small><br><small>{{ $productionWithYield }} with yield data</small></div></div>
</div>
<div class="row g-4 mt-1">
<div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-title">Net Profit</div><div class="card-value">RM {{ number_format($netProfit, 2) }}</div><small>Completed revenue less expenses</small></div></div>
<div class="col-lg-3 col-md-6"><div class="dashboard-card"><div class="card-title">Inventory</div><div class="card-value">{{ $totalMaterials }}</div><small>{{ $lowStockItems }} low-stock materials</small></div></div>
</div>
</div>

@endsection

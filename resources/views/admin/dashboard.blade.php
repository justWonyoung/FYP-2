@extends('layouts.app')


@php

$pageTitle = "Dashboard | DBFM";

$moduleTitle = "Dashboard";

$moduleSubtitle = "Operational Overview";

@endphp


@section('content')


<!-- PAGE HEADER -->

<div class="page-header">


<div class="d-flex justify-content-between align-items-start gap-4">


<div>

<h1>
Welcome back, Administrator
</h1>


<p>
Here is today's DBFM operational overview.
</p>


<div class="text-muted small mt-3">

Last updated:
{{ now()->format('d M Y, h:i A') }}

</div>

</div>



</div>


</div>



@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

{{ session('success') }}


<button 
class="btn-close"
data-bs-dismiss="alert">
</button>


</div>

@endif







<!-- KPI SECTION -->


<div class="row g-4 mb-4">



<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="d-flex justify-content-between">


<div>


<div class="card-title">

ACTIVE ORDERS

</div>


<div class="card-value">

{{ $activeOrders }}

</div>


<div class="stat-change">

↑ 12% from last month

</div>


</div>



<div class="stat-icon">

<svg class="stat-icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h5"/></svg>

</div>


</div>


</div>


</div>








<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="d-flex justify-content-between">


<div>


<div class="card-title">

INVENTORY ITEMS

</div>


<div class="card-value">

{{ $totalMaterials }}

</div>


<div class="stat-change">

Healthy stock level

</div>


</div>



<div class="stat-icon">

<svg class="stat-icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.8 7.5 4.2 7.5-4.2M12 12v9"/></svg>

</div>


</div>


</div>


</div>








<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="d-flex justify-content-between">


<div>


<div class="card-title">

PENDING APPROVALS

</div>


<div class="card-value text-danger">

{{ $pendingApprovals->count() }}

</div>


<div class="text-danger small mt-2">

Requires attention

</div>


</div>



<div class="stat-icon">

<svg class="stat-icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 3h12M6 21h12M7 3c0 4 5 4 5 9s-5 5-5 9M17 3c0 4-5 4-5 9s5 5 5 9"/></svg>

</div>


</div>


</div>


</div>








<div class="col-lg-3 col-md-6">


<div class="dashboard-card">


<div class="d-flex justify-content-between">


<div>


<div class="card-title">

MONTHLY EXPENSE

</div>


<div class="card-value">

RM {{ number_format($monthlyExpense,2) }}

</div>


<div class="stat-change">

Current month

</div>


</div>



<div class="stat-icon">

<svg class="stat-icon-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h.01M17 15h.01M12 9.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/></svg>

</div>

</div>


</div>


</div>



</div>









<!-- OPERATION SUMMARY -->

<div class="row g-4 mb-4">
    <div class="col-lg-6"><div class="section-card chart-card"><div class="section-title">Customer Order Status</div><div class="section-subtitle mb-3">Current order distribution</div><div class="chart-container"><canvas id="adminOrderChart" aria-label="Customer order status chart"></canvas></div></div></div>
    <div class="col-lg-6"><div class="section-card chart-card"><div class="section-title">Purchase Request Status</div><div class="section-subtitle mb-3">Approval queue overview</div><div class="chart-container"><canvas id="adminPurchaseChart" aria-label="Purchase request status chart"></canvas></div></div></div>
    <div class="col-lg-6"><div class="section-card chart-card"><div class="section-title">Production Status</div><div class="section-subtitle mb-3">Manufacturing batches</div><div class="chart-container"><canvas id="adminProductionChart" aria-label="Production status chart"></canvas></div></div></div>
    <div class="col-lg-6"><div class="section-card chart-card"><div class="section-title">Inventory Health</div><div class="section-subtitle mb-3">Material stock condition</div><div class="chart-container"><canvas id="adminInventoryChart" aria-label="Inventory health chart"></canvas></div></div></div>
</div>


{{-- Duplicate Production Overview and Inventory Health summaries are represented by the charts above. --}}
{{--
<div class="row g-4 mb-4">


<div class="col-lg-6">


<div class="section-card">


<div class="section-title">

Production Overview

</div>


<div class="section-subtitle mb-4">

Current manufacturing status

</div>



<div class="row text-center">


<div class="col">


<h3 class="fw-bold">

{{ $completedProduction }}

</h3>


<small class="text-muted">

Completed

</small>


</div>



<div class="col">


<h3 class="fw-bold">

{{ $runningProduction }}

</h3>


<small class="text-muted">

Running

</small>


</div>



<div class="col">


<h3 class="fw-bold">

{{ $pendingProduction }}

</h3>


<small class="text-muted">

Pending

</small>


</div>


</div>


</div>


</div>





<div class="col-lg-6">


<div class="section-card">


<div class="section-title">

Inventory Health

</div>


<div class="section-subtitle mb-4">

Material availability status

</div>



<div class="d-flex justify-content-between mb-3">

<span>

Raw Material

</span>


@if($criticalItems > 0)

<span class="badge bg-danger">
Critical
</span>


@elseif($lowStockItems > 0)

<span class="badge bg-warning text-dark">
Attention Required
</span>


@else

<span class="badge bg-success">
Healthy
</span>


@endif


</div>



<div class="d-flex justify-content-between mb-3">

<span>

Low Stock Alert

</span>


<span class="badge bg-warning text-dark">

{{ $lowStockItems }}

</span>


</div>




<div class="d-flex justify-content-between">

<span>

Critical Items

</span>


<span class="badge bg-danger">

{{ $criticalItems }}

</span>


</div>


</div>


</div>



</div>









--}}

<!-- APPROVAL QUEUE -->


<div class="section-card">


<div class="mb-4">


<div class="section-title">

Approval Queue

</div>


<div class="section-subtitle">

Purchase requests requiring final administrator review

</div>


</div>







<div class="table-responsive">


<table class="table align-middle">


<thead>


<tr>

<th>
PR ID
</th>

<th>
ITEM
</th>

<th>
AMOUNT
</th>

<th>
FINANCE REVIEW
</th>

<th>
FINANCE REMARK
</th>

<th>
STATUS
</th>

<th>
ACTION
</th>


</tr>


</thead>



<tbody>



@forelse($pendingApprovals as $pr)


<tr>


<td>

<strong>

{{ $pr->request_no }}

</strong>

</td>




<td>

{{ $pr->material_item }}

<br>

<small class="text-muted">

{{ $pr->quantity }} {{ $pr->unit }}

</small>


</td>




<td>

<strong>

RM {{ number_format($pr->estimated_cost,2) }}

</strong>


</td>




<td>


<span class="badge {{ $pr->finance_status === 'approved' ? 'bg-success' : ($pr->finance_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">

{{ ucfirst($pr->finance_status) }}

</span>


</td>




<td>

<small class="text-muted">

{{ $pr->finance_remark ?? 'None' }}

</small>


</td>




<td>


<span class="badge {{ $pr->approval_status === 'approved' ? 'bg-success' : ($pr->approval_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">

{{ ucfirst($pr->approval_status) }}

</span>


</td>




<td>


<a href="{{ route('admin.purchase.review', $pr->getKey()) }}"
class="btn btn-primary btn-sm">

Review

</a>


</td>



</tr>



@empty


<tr>


<td colspan="7" class="text-center py-5 text-muted">


<i class="bi bi-inbox fs-2 d-block mb-3"></i>


No Purchase Requests awaiting approval.


</td>


</tr>



@endforelse



</tbody>


</table>


</div>


</div>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const adminChartBase = { responsive:true, maintainAspectRatio:false, animation:{duration:850,easing:'easeOutQuart'}, plugins:{legend:{position:'bottom',labels:{usePointStyle:true,padding:14}},tooltip:{padding:10,cornerRadius:8}} };
new Chart(document.getElementById('adminOrderChart'), {type:'doughnut',data:{labels:@json($orderSummary->keys()),datasets:[{data:@json($orderSummary->values()),backgroundColor:['#f59e0b','#7c3aed','#16a34a','#dc2626'],borderWidth:3,borderColor:'#fff'}]},options:adminChartBase});
new Chart(document.getElementById('adminPurchaseChart'), {type:'bar',data:{labels:@json($purchaseSummary->keys()),datasets:[{label:'Requests',data:@json($purchaseSummary->values()),backgroundColor:@json($purchaseSummary->keys()->map(fn ($status) => match (strtolower($status)) { 'approved' => '#16A34A', 'rejected' => '#DC2626', default => '#F59E0B' })->values()),borderRadius:8,maxBarThickness:46}]},options:{...adminChartBase,indexAxis:'y',plugins:{...adminChartBase.plugins,legend:{display:false}},scales:{x:{beginAtZero:true,grid:{color:'#e5e7eb'},ticks:{precision:0}},y:{grid:{display:false}}}}});
new Chart(document.getElementById('adminProductionChart'), {type:'doughnut',data:{labels:['Completed','Running','Pending'],datasets:[{data:[@json($completedProduction),@json($runningProduction),@json($pendingProduction)],backgroundColor:['#16a34a','#7c3aed','#f59e0b'],borderWidth:3,borderColor:'#fff'}]},options:adminChartBase});
new Chart(document.getElementById('adminInventoryChart'), {type:'bar',data:{labels:['Healthy','Low Stock','Critical'],datasets:[{label:'Materials',data:[@json(max($totalMaterials - $lowStockItems, 0)),@json(max($lowStockItems - $criticalItems, 0)),@json($criticalItems)],backgroundColor:['#16a34a','#f59e0b','#dc2626'],borderRadius:8,maxBarThickness:42}]},options:{...adminChartBase,indexAxis:'y',plugins:{...adminChartBase.plugins,legend:{display:false}},scales:{x:{beginAtZero:true,grid:{color:'#e5e7eb'},ticks:{precision:0}},y:{grid:{display:false}}}}});
</script>
@endpush
@endsection

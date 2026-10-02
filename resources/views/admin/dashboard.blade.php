@extends('layouts.app')


@php

$pageTitle = "Dashboard | Beauty Kasih";

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
Here is today's Beauty Kasih operational overview.
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

24

</div>


<div class="stat-change">

↑ 12% from last month

</div>


</div>



<div class="stat-icon">

<i class="bi bi-cart-check"></i>

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

138

</div>


<div class="stat-change">

Healthy stock level

</div>


</div>



<div class="stat-icon">

<i class="bi bi-box-seam"></i>

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

<i class="bi bi-hourglass-split"></i>

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

RM 42K

</div>


<div class="stat-change">

Current month

</div>


</div>



<div class="stat-icon">

<i class="bi bi-cash-stack"></i>

</div>


</div>


</div>


</div>



</div>









<!-- OPERATION SUMMARY -->


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

12

</h3>


<small class="text-muted">

Completed

</small>


</div>



<div class="col">


<h3 class="fw-bold">

4

</h3>


<small class="text-muted">

Running

</small>


</div>



<div class="col">


<h3 class="fw-bold">

2

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


<span class="badge bg-success">

Healthy

</span>


</div>



<div class="d-flex justify-content-between mb-3">

<span>

Low Stock Alert

</span>


<span class="badge bg-warning text-dark">

0

</span>


</div>




<div class="d-flex justify-content-between">

<span>

Critical Items

</span>


<span class="badge bg-danger">

0

</span>


</div>


</div>


</div>



</div>









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


<span class="badge bg-info text-dark">

{{ ucfirst($pr->finance_status) }}

</span>


</td>




<td>

<small class="text-muted">

{{ $pr->finance_remark ?? 'None' }}

</small>


</td>




<td>


<span class="badge bg-warning text-dark">

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



@endsection
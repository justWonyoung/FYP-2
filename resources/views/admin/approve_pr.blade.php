@extends('layouts.app')


@php

$pageTitle = "Purchase Approval | Beauty Kasih";

$moduleTitle = "Purchase Approval";

$moduleSubtitle = "Review purchase requests approved by Finance";

@endphp



@section('content')


<div class="page-header">

<h1>
Purchase Request Approval
</h1>

<p>
Requests waiting for final administrator approval.
</p>

</div>



@if(session('success'))

<div class="alert alert-success">

{{ session('success') }}

</div>

@endif





<div class="dashboard-card">


<div class="table-responsive">


<table class="table align-middle">


<thead>

<tr>

<th>
PR ID
</th>


<th>
Supplier
</th>


<th>
Material
</th>


<th>
Amount
</th>


<th>
Finance Status
</th>


<th>
Action
</th>


</tr>

</thead>




<tbody>


@forelse($requests as $pr)


<tr>


<td>

{{ $pr->request_no }}

</td>



<td>

{{ $pr->supplier_name }}

</td>



<td>

{{ $pr->material_item }}

</td>



<td>

RM {{ number_format($pr->estimated_cost,2) }}

</td>



<td>

<span class="badge bg-success">

Finance Approved

</span>

</td>




<td>


<a href="{{ route('admin.purchase.review',$pr->purchase_request_id) }}"
class="btn btn-sm btn-primary">

Review

</a>


</td>



</tr>


@empty


<tr>

<td colspan="6"
class="text-center">

No purchase request waiting for approval.

</td>


</tr>


@endforelse



</tbody>


</table>


</div>


</div>



@endsection
@extends('layouts.app')


@php

$pageTitle = "Purchase Report | Beauty Kasih";

$moduleTitle = "Purchase Report";

$moduleSubtitle = "Purchase Request Analysis";

@endphp



@section('content')


<div class="page-header">


<h1>
Purchase Report
</h1>


<p>
Purchase request monitoring and approval history.
</p>


</div>





<div class="section-card">


<div class="section-title">

Purchase Request List

</div>


<div class="section-subtitle mb-4">

All purchase requests recorded in the system.

</div>





<div class="table-responsive">


<table class="table align-middle">


<thead>

<tr>

<th>
PR ID
</th>


<th>
Material
</th>


<th>
Quantity
</th>


<th>
Estimated Cost
</th>


<th>
Finance Status
</th>


<th>
Approval Status
</th>


</tr>

</thead>




<tbody>



@forelse($purchaseRequests as $pr)



<tr>


<td>

<strong>

{{ $pr->request_no }}

</strong>

</td>




<td>

{{ $pr->material_item }}

</td>





<td>

{{ number_format($pr->quantity,2) }}

{{ $pr->unit }}

</td>





<td>

RM {{ number_format($pr->estimated_cost,2) }}

</td>





<td>


<span class="badge bg-info text-dark">

{{ ucfirst($pr->finance_status) }}

</span>


</td>





<td>


@if($pr->approval_status == 'approved')


<span class="badge bg-success">

Approved

</span>


@elseif($pr->approval_status == 'rejected')


<span class="badge bg-danger">

Rejected

</span>


@else


<span class="badge bg-warning text-dark">

Pending

</span>


@endif



</td>



</tr>



@empty


<tr>


<td colspan="6" class="text-center py-5">

No purchase records found.

</td>


</tr>


@endforelse



</tbody>



</table>



</div>


</div>



@endsection
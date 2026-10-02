@extends('layouts.app')


@php

$pageTitle = "Production | Beauty Kasih";

$moduleTitle = "Production Control";

$moduleSubtitle = "Batch Processing & Yield Monitoring";

@endphp



@section('content')



<!-- ACTION HEADER -->

<div class="d-flex justify-content-end align-items-center mb-4">


<a href="{{ route('production.create') }}" 
class="btn btn-primary fw-bold">


<i class="bi bi-gear-wide-connected me-1"></i>


Start New Batch


</a>


</div>







@if(session('success'))


<div class="alert alert-success alert-dismissible fade show">


<i class="bi bi-check-circle me-2"></i>


{{ session('success') }}


<button 
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>


</div>


@endif







<div class="section-card">


<div class="section-title mb-3">

Production Batches

</div>



<div class="section-subtitle mb-4">

Track manufacturing batches, output performance and material efficiency.

</div>






<div class="table-responsive">


<table class="table align-middle">



<thead>

<tr>


<th>
BATCH NO
</th>


<th>
ORDER NO
</th>


<th>
PRODUCT
</th>


<th>
OUTPUT PERFORMANCE
</th>


<th>
MATERIAL WASTAGE
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



@forelse($batches as $batch)



<tr>



<td>


<strong>

{{ $batch->batch_number }}

</strong>


</td>






<td>

{{ $batch->customer_order_no }}

</td>






<td>


<strong>

{{ $batch->product_name }}

</strong>


</td>








<!-- OUTPUT -->


<td>


<div>


<span class="text-muted small">

Planned Output

</span>


<br>


{{ number_format($batch->planned_output,2) }}

{{ $batch->unit }}



</div>






<div class="mt-2">


<span class="text-muted small">

Actual Output

</span>


<br>


<strong class="text-success">


{{ number_format($batch->actual_output,2) }}

{{ $batch->unit }}


</strong>


</div>






@php


$yield = 0;


if($batch->planned_output > 0)

{

$yield =

($batch->actual_output /

$batch->planned_output)

*100;

}


@endphp






<div class="mt-2">


<span class="text-muted small">

Yield Performance

</span>


<br>



@if($yield >= 90)


<span class="badge bg-success">

{{ number_format($yield,2) }}%

</span>



@elseif($yield >=70)


<span class="badge bg-warning text-dark">

{{ number_format($yield,2) }}%

</span>



@else


<span class="badge bg-danger">

{{ number_format($yield,2) }}%

</span>


@endif



</div>





</td>









<!-- WASTE -->


<td>


@php

$totalWaste = $batch->wastes->sum('quantity_wasted');

@endphp





@if($totalWaste > 0)


<span class="badge bg-danger">


<i class="bi bi-exclamation-triangle me-1"></i>


{{ number_format($totalWaste,2) }}

Wasted


</span>



@else


<span class="badge bg-success">


0 Wasted


</span>



@endif



</td>









<!-- STATUS -->


<td>



@if($batch->status === 'in_progress')



<span class="badge bg-warning text-dark">


<i class="bi bi-hourglass-split me-1"></i>


In Progress


</span>





@else



<span class="badge bg-success">


<i class="bi bi-check-circle me-1"></i>


Completed


</span>



@endif



</td>









<!-- ACTION -->


<td>



<a 
href="{{ route('production.log',$batch->production_id) }}"
class="btn btn-outline-primary btn-sm mb-2">


<i class="bi bi-clipboard-data me-1"></i>


Log Material/Waste


</a>







@if($batch->status === 'in_progress')



<form method="POST"

action="{{ route('production.output',$batch->production_id) }}"

class="mb-2">


@csrf



<div class="input-group input-group-sm">


<input

type="number"

step="0.01"

name="actual_output"

class="form-control"

placeholder="Actual Output"

required>


<button class="btn btn-success">


Update


</button>


</div>


</form>









<form method="POST"

action="{{ route('production.complete',$batch->production_id) }}">


@csrf



<button

class="btn btn-dark btn-sm"

onclick="return confirm('Complete this production batch?')">


<i class="bi bi-check-circle me-1"></i>


Complete Batch


</button>


</form>



@else



<span class="text-success small">


Production Completed


</span>


@endif




</td>





</tr>





@empty



<tr>


<td colspan="7"

class="text-center text-muted py-5">


<i class="bi bi-gear-wide-connected fs-2 d-block mb-3"></i>


No production batches created.



</td>


</tr>



@endforelse




</tbody>


</table>


</div>


</div>





@endsection
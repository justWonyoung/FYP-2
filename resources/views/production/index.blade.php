@extends('layouts.app')


@php

$pageTitle = "Production | Beauty Kasih";

$moduleTitle = "Production Control";

$moduleSubtitle = "Batch Processing & Yield Monitoring";

@endphp



@section('content')



<!-- ACTION HEADER -->

<div class="d-flex justify-content-end align-items-center mb-4">


<a href="#" class="btn btn-primary fw-bold">


<i class="bi bi-gear-wide-connected me-1"></i>


Start New Batch


</a>


</div>







@if(session('success'))


<div class="alert alert-success alert-dismissible fade show" role="alert">


<i class="bi bi-check-circle me-2"></i>


{{ session('success') }}



<button 
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>


</div>


@endif







<!-- PRODUCTION TABLE -->


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







<td>


<div>


<span class="text-muted small">

Planned

</span>


<br>


{{ $batch->planned_output }} {{ $batch->unit }}



</div>





<div class="mt-2">


<span class="text-muted small">

Actual Yield

</span>


<br>



<strong 
class="{{ $batch->actual_output > 0 ? 'text-success':'text-muted' }}">


{{ $batch->actual_output }} {{ $batch->unit }}



</strong>



</div>



</td>









<td>



@php

$totalWaste = $batch->wastes->sum('quantity_wasted');

@endphp






@if($totalWaste > 0)


<span class="badge bg-danger">


<i class="bi bi-exclamation-triangle me-1"></i>


{{ $totalWaste }} kg/L Wasted


</span>



@else


<span class="badge bg-success">


0 Wasted


</span>



@endif



</td>








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








<td>



<a 
href="#"
class="btn btn-outline-primary btn-sm">


<i class="bi bi-clipboard-data me-1"></i>


Log Material/Waste


</a>



</td>







</tr>







@empty



<tr>


<td colspan="7" class="text-center text-muted py-5">


<i class="bi bi-gear-wide-connected fs-2 d-block mb-3"></i>


No production batches created.


<br>


Click "Start New Batch" to begin.


</td>


</tr>



@endforelse





</tbody>



</table>



</div>


</div>






@endsection
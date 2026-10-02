@extends('layouts.app')


@php

$pageTitle = "Material Usage | Beauty Kasih";

$moduleTitle = "Production Control";

$moduleSubtitle = "Material Usage & Waste Tracking";

@endphp





@section('content')





@if(session('success'))


<div class="alert alert-success alert-dismissible fade show">


{{ session('success') }}


<button 
type="button"
class="btn-close"
data-bs-dismiss="alert">
</button>


</div>


@endif







<div class="page-header">


<h1>

{{ $batch->batch_number }}

</h1>


<p>

{{ $batch->product_name }}

material monitoring and production tracking.

</p>


</div>









<!-- FORMS -->

<div class="row g-4">





<!-- MATERIAL USAGE -->


<div class="col-lg-6">


<div class="section-card">


<div class="section-title mb-4">

Log Material Usage

</div>





<form method="POST"

action="{{ route('production.usage',$batch->production_id) }}">


@csrf





<div class="mb-3">


<label class="form-label">

Material

</label>



<select

name="material_id"

class="form-control"

required>


<option value="">

Select Material

</option>



@foreach($materials as $material)


<option value="{{ $material->material_id }}">


{{ $material->material_name }}


(Current:

{{ $material->current_stock }}

{{ $material->unit }})


</option>


@endforeach



</select>


</div>







<div class="mb-3">


<label class="form-label">

Quantity Used

</label>



<input

type="number"

step="0.01"

name="quantity_used"

class="form-control"

required>


</div>






<button class="btn btn-primary">


Save Usage


</button>



</form>



</div>


</div>









<!-- MATERIAL WASTE -->


<div class="col-lg-6">


<div class="section-card">


<div class="section-title mb-4">

Log Material Waste

</div>






<form method="POST"

action="{{ route('production.waste',$batch->production_id) }}">


@csrf






<div class="mb-3">


<label class="form-label">

Material

</label>



<select

name="material_id"

class="form-control"

required>


<option value="">

Select Material

</option>



@foreach($materials as $material)


<option value="{{ $material->material_id }}">


{{ $material->material_name }}


(Current:

{{ $material->current_stock }}

{{ $material->unit }})


</option>


@endforeach



</select>


</div>








<div class="mb-3">


<label class="form-label">

Quantity Wasted

</label>



<input

type="number"

step="0.01"

name="quantity_wasted"

class="form-control"

required>


</div>








<div class="mb-3">


<label class="form-label">

Waste Reason

</label>



<input

type="text"

name="waste_reason"

class="form-control"

placeholder="Example: Spillage">


</div>








<button class="btn btn-danger">


Save Waste


</button>



</form>



</div>


</div>




</div>









<!-- USAGE HISTORY -->


<div class="section-card mt-4">


<div class="section-title mb-3">

Material Usage History

</div>


<div class="section-subtitle mb-4">

Materials consumed for this production batch.

</div>






<div class="table-responsive">


<table class="table align-middle">


<thead>


<tr>

<th>
Material
</th>


<th>
Quantity Used
</th>


<th>
Date
</th>


</tr>


</thead>



<tbody>



@forelse($batch->materialUsages as $usage)



<tr>


<td>


<strong>

{{ $usage->material->material_name }}

</strong>


</td>



<td>


{{ number_format($usage->quantity_used,2) }}

{{ $usage->material->unit }}


</td>



<td>


{{ $usage->created_at->format('d M Y H:i') }}


</td>


</tr>



@empty



<tr>


<td colspan="3"

class="text-center text-muted py-4">


No material usage recorded.


</td>


</tr>



@endforelse



</tbody>


</table>


</div>


</div>









<!-- WASTE HISTORY -->


<div class="section-card mt-4">


<div class="section-title mb-3">

Material Waste History

</div>


<div class="section-subtitle mb-4">

Waste generated during production.

</div>






<div class="table-responsive">


<table class="table align-middle">


<thead>


<tr>


<th>
Material
</th>


<th>
Quantity Waste
</th>


<th>
Reason
</th>


<th>
Date
</th>


</tr>


</thead>



<tbody>



@forelse($batch->wastes as $waste)



<tr>



<td>


<strong>

{{ $waste->material->material_name }}

</strong>


</td>





<td>


{{ number_format($waste->quantity_wasted,2) }}

{{ $waste->material->unit }}


</td>





<td>


{{ $waste->waste_reason ?? 'Not specified' }}


</td>





<td>


{{ $waste->created_at->format('d M Y H:i') }}


</td>




</tr>



@empty



<tr>


<td colspan="4"

class="text-center text-muted py-4">


No waste recorded.


</td>


</tr>



@endforelse



</tbody>


</table>


</div>


</div>






@endsection
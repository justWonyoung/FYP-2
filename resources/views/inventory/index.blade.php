@extends('layouts.app')


@php

$pageTitle = "Inventory | Beauty Kasih";
$moduleTitle = "Inventory Management";
$moduleSubtitle = "Material Stock & Availability Monitoring";

@endphp



@section('content')



<div class="d-flex justify-content-end mb-4">


    <a href="{{ route('inventory.receiving.history') }}"
       class="btn btn-outline-primary me-2">


        <i class="bi bi-clock-history me-1"></i>

        Receiving History


    </a>





    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#receiveMaterialModal">


        <i class="bi bi-box-seam me-1"></i>

        Receive Material


    </button>


</div>





@if(session('success'))


<div class="alert alert-success alert-dismissible fade show">


{{ session('success') }}


<button class="btn-close"
data-bs-dismiss="alert">
</button>


</div>


@endif







<!-- ============================= -->
<!-- RECEIVE MATERIAL MODAL -->
<!-- ============================= -->


<div class="modal fade"
id="receiveMaterialModal"
tabindex="-1">



<div class="modal-dialog">



<div class="modal-content">



<form method="POST"
action="{{ route('inventory.store') }}">


@csrf




<div class="modal-header">


<h5 class="modal-title">

Receive Material

</h5>



<button type="button"
class="btn-close"
data-bs-dismiss="modal">

</button>


</div>







<div class="modal-body">






<div class="mb-3">


<label class="form-label">

Material Name

</label>


<input type="text"
name="material_name"
class="form-control"
placeholder="Example: glycerin"
required>


</div>







<div class="mb-3">


<label class="form-label">

Supplier Name

</label>


<input type="text"
name="supplier_name"
class="form-control"
placeholder="Supplier name"
required>


</div>








<div class="mb-3">


<label class="form-label">

Quantity Received

</label>


<input type="number"
step="0.01"
name="quantity_received"
class="form-control"
placeholder="Example: 500"
required>


</div>








<div class="mb-3">


<label class="form-label">

Unit

</label>



<select name="unit"
class="form-select"
required>



<option value="kg">

kg

</option>




<option value="L">

L

</option>




<option value="pcs">

pcs

</option>



</select>


</div>









<div class="mb-3">


<label class="form-label">

Received Date

</label>


<input type="date"
name="received_date"
class="form-control"
value="{{ date('Y-m-d') }}"
required>


</div>








<div class="mb-3">


<label class="form-label">

Remarks

</label>



<textarea
name="remarks"
class="form-control"
rows="3"
placeholder="Optional notes"></textarea>


</div>






</div>









<div class="modal-footer">



<button type="button"
class="btn btn-secondary"
data-bs-dismiss="modal">

Cancel

</button>





<button type="submit"
class="btn btn-primary">


Receive Material


</button>




</div>







</form>




</div>



</div>



</div>









<!-- ============================= -->
<!-- STOCK TABLE -->
<!-- ============================= -->



<div class="section-card">



<div class="section-title mb-4">

Current Stock Levels

</div>








<div class="table-responsive">



<table class="table align-middle">





<thead>



<tr>


<th>
MATERIAL NAME
</th>


<th>
TYPE
</th>


<th>
CURRENT STOCK
</th>


<th>
MINIMUM STOCK
</th>


<th>
STATUS
</th>


</tr>



</thead>








<tbody>





@forelse($materials as $item)





<tr>






<td>



<strong>


{{ $item->material_name }}


</strong>



</td>







<td>



<span class="badge bg-secondary">


{{ $item->material_type }}


</span>



</td>








<td>



<strong class="text-primary">



{{ number_format($item->current_stock,2) }}


{{ $item->unit }}



</strong>



</td>








<td>



{{ number_format($item->minimum_stock,2) }}


{{ $item->unit }}



</td>









<td>





@if($item->current_stock <= $item->minimum_stock)





<span class="badge bg-warning text-dark">


Low Stock


</span>





@else






<span class="badge bg-success">


In Stock


</span>





@endif





</td>







</tr>






@empty





<tr>



<td colspan="5"
class="text-center text-muted py-5">


No materials registered.


</td>



</tr>






@endforelse







</tbody>





</table>




</div>



</div>






@endsection
@extends('layouts.app')

@section('content')

<div class="container-fluid" style="max-width:800px;">

<div class="card stat-card p-4">


<h5 class="fw-bold mb-4">
<i class="bi bi-box-seam me-2"></i>
Receive Delivered Material
</h5>




<form action="{{ route('inventory.store') }}" method="POST">

@csrf






<div class="mb-4">

<label class="form-label fw-bold">

Select Approved Purchase Request

</label>


<select 
id="purchase_request_id"
name="purchase_request_id"
class="form-select"
required>


<option value="">

-- Select Purchase Request --

</option>



@foreach($approvedPRs as $pr)


<option 

value="{{ $pr->purchase_request_id }}">


{{ $pr->request_no }}

-

{{ $pr->material_item }}

-

{{ $pr->supplier_name }}

-

{{ $pr->quantity }}

{{ $pr->unit }}


</option>



@endforeach


</select>


</div>









<div class="card bg-light p-3 mb-4">


<h6 class="fw-bold mb-3">

Purchase Request Details

</h6>



<p class="mb-1">

<strong>Material:</strong>

<span id="material_name">

-

</span>

</p>




<p class="mb-1">

<strong>Supplier:</strong>

<span id="supplier_name">

-

</span>

</p>





<p class="mb-0">

<strong>Ordered Quantity:</strong>

<span id="ordered_quantity">

-

</span>

</p>




</div>









<div class="row g-3 mb-3">





<div class="col-md-6">


<label class="form-label fw-bold">

Quantity Received

</label>



<input

type="number"

step="0.01"

id="quantity_received"

name="quantity_received"

class="form-control"

placeholder="Quantity received"

required>


</div>






<div class="col-md-6">


<label class="form-label fw-bold">

Received Date

</label>



<input

type="date"

name="received_date"

class="form-control"

value="{{ date('Y-m-d') }}"

required>


</div>



</div>









<div class="mb-4">


<label class="form-label fw-bold">

Remarks

</label>


<textarea

name="remarks"

class="form-control"

rows="3"

placeholder="Example: Goods received in good condition"></textarea>


</div>










<div class="d-flex justify-content-end gap-2">



<a href="{{ route('inventory.index') }}"

class="btn btn-secondary">

Cancel

</a>





<button

type="submit"

class="btn btn-success px-4">


<i class="bi bi-download me-1"></i>


Confirm Receiving


</button>



</div>





</form>


</div>


</div>








<script>


document
.getElementById('purchase_request_id')
.addEventListener(
'change',
function(){


let prID = this.value;



if(!prID)
{

document.getElementById('material_name').innerHTML = '-';

document.getElementById('supplier_name').innerHTML = '-';

document.getElementById('ordered_quantity').innerHTML = '-';

document.getElementById('quantity_received').value = '';

return;

}





fetch(
'/inventory/pr-details/' + prID
)

.then(response => response.json())

.then(data => {



document.getElementById('material_name')
.innerHTML =
data.material;



document.getElementById('supplier_name')
.innerHTML =
data.supplier;



document.getElementById('ordered_quantity')
.innerHTML =
data.quantity + ' ' + data.unit;





document.getElementById('quantity_received')
.value =
data.quantity;



});



});


</script>



@endsection
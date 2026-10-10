@extends('layouts.app')


@php

$pageTitle = "Start Production Batch | DBFM";

$moduleTitle = "Production Control";

$moduleSubtitle = "Create New Manufacturing Batch";

@endphp



@section('content')



<div class="page-header">


<h1>

Start New Production Batch

</h1>


<p>

Create a new manufacturing production record.

</p>


</div>





<div class="section-card">


<form method="POST" action="{{ route('production.store') }}">


@csrf



<div class="row g-4">



<div class="col-md-6">


<label class="form-label" for="customer_order_no">

Batch Number

</label>


<input 
type="text"
name="batch_number"
class="form-control"
placeholder="BATCH-003"
required>


</div>





<div class="col-md-6">


<label class="form-label">

Customer Order No

</label>


<select id="customer_order_no" name="customer_order_no" class="form-select" required @disabled($customerOrders->isEmpty())>
<option value="">Select customer order</option>
@foreach($customerOrders as $customerOrder)
<option value="{{ $customerOrder->order_no }}" @selected(old('customer_order_no') === $customerOrder->order_no)>
{{ $customerOrder->order_no }} - {{ $customerOrder->customer?->customer_name ?? 'Unnamed customer' }}
</option>
@endforeach
</select>
@if($customerOrders->isEmpty())
<div class="form-text text-danger">No eligible customer orders are available.</div>
@endif
@error('customer_order_no')<div class="text-danger small">{{ $message }}</div>@enderror


</div>





<div class="col-md-6">


<label class="form-label" for="product_name">

Product Name

</label>


<select id="product_name" name="product_name" class="form-select" required @disabled($customerOrders->isEmpty())>
<option value="">Select product</option>
</select>
<div class="form-text">Products are limited to the selected customer order.</div>
@error('product_name')<div class="text-danger small">{{ $message }}</div>@enderror


</div>





<div class="col-md-6">


<label class="form-label">

Planned Output

</label>


<input 
type="number"
step="0.01"
name="planned_output"
class="form-control"
required>


</div>





<div class="col-md-6">


<label class="form-label">

Unit

</label>


<select 
name="unit"
class="form-control">


<option value="pcs">

pcs

</option>


<option value="kg">

kg

</option>


<option value="L">

L

</option>


</select>


</div>





<div class="col-md-6">


<label class="form-label">

Start Date

</label>


<input 
type="date"
name="start_date"
class="form-control"
required>


</div>



</div>





<div class="mt-4">


<button class="btn btn-primary">

Create Batch

</button>



<a href="{{ route('production.index') }}"
class="btn btn-secondary">


Cancel


</a>


</div>




</form>


</div>




@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderSelect = document.getElementById('customer_order_no');
    const productSelect = document.getElementById('product_name');
    const orders = @json($customerOrders->mapWithKeys(fn ($order) => [$order->order_no => $order->items->map(fn ($item) => ['product_name' => $item->product_name, 'quantity' => $item->quantity])])->all());
    const oldProduct = @json(old('product_name'));

    function refreshProducts() {
        const items = orders[orderSelect.value] || [];
        productSelect.innerHTML = '<option value="">Select product</option>';
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.product_name;
            option.textContent = `${item.product_name} (ordered: ${item.quantity})`;
            option.selected = item.product_name === oldProduct;
            productSelect.appendChild(option);
        });
        productSelect.disabled = items.length === 0;
    }

    orderSelect.addEventListener('change', refreshProducts);
    refreshProducts();
});
</script>

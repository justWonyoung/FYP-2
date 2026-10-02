@extends('layouts.app')


@php

$pageTitle = "Start Production Batch | Beauty Kasih";

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


<label class="form-label">

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


<input 
type="text"
name="customer_order_no"
class="form-control"
placeholder="CO-091"
required>


</div>





<div class="col-md-6">


<label class="form-label">

Product Name

</label>


<input 
type="text"
name="product_name"
class="form-control"
placeholder="Body Lotion 500ml"
required>


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
@extends('layouts.app')


@php

$pageTitle = "Finance | Beauty Kasih";

$moduleTitle = "Finance";

$moduleSubtitle = "Expense Management & Financial Control";

@endphp



@section('content')





<div class="d-flex justify-content-between align-items-center mb-4">


<h4 class="m-0 fw-bold">

Expense Management

</h4>





<a href="{{ route('finance.report') }}"
class="btn btn-outline-primary">


<i class="bi bi-bar-chart me-1"></i>


Financial Report


</a>



</div>









@if(session('success'))


<div class="alert alert-success alert-dismissible fade show mb-4"
role="alert">


{{ session('success') }}



<button type="button"
class="btn-close"
data-bs-dismiss="alert"
aria-label="Close">

</button>



</div>


@endif







<div class="row g-4">






<!-- Expense Input Form -->


<div class="col-md-4">


<div class="card stat-card p-4">


<h6 class="fw-bold mb-3">


<i class="bi bi-plus-circle me-1"></i>


Log Operational Expense


</h6>






<form action="{{ route('finance.expenses.store') }}"
method="POST">


@csrf






<div class="mb-3">


<label class="form-label fw-bold">

Expense Category

</label>



<select name="expense_category"
class="form-select"
required>



<option value="Raw Materials">

Raw Materials

</option>



<option value="Packaging">

Packaging

</option>



<option value="Labor / Utility">

Labor / Utility

</option>



<option value="Logistics">

Logistics

</option>



</select>



</div>








<div class="mb-3">


<label class="form-label fw-bold">

Description

</label>



<input type="text"
name="description"
class="form-control"
placeholder="e.g. Factory Electricity Bill"
required>



</div>








<div class="mb-3">


<label class="form-label fw-bold">

Amount (RM)

</label>



<input type="number"
step="0.01"
name="amount"
class="form-control"
placeholder="1200.00"
required>



</div>








<div class="mb-4">


<label class="form-label fw-bold">

Expense Date

</label>



<input type="date"
name="expense_date"
class="form-control"
value="{{ date('Y-m-d') }}"
required>



</div>








<button type="submit"
class="btn btn-primary w-100">


<i class="bi bi-save me-1"></i>


Save Expense Record


</button>





</form>



</div>


</div>









<!-- Expense Logs Table -->


<div class="col-md-8">


<div class="card stat-card p-3">


<h6 class="m-0 fw-bold mb-3">

Recent Expense Logs

</h6>






<div class="table-responsive">



<table class="table align-middle">





<thead class="table-light">


<tr>


<th>

DATE

</th>



<th>

CATEGORY

</th>



<th>

DESCRIPTION

</th>



<th>

AMOUNT (RM)

</th>



</tr>


</thead>








<tbody>



@forelse($expenses as $exp)



<tr>



<td>

{{ $exp->expense_date }}

</td>





<td>


<span class="badge bg-secondary">


{{ $exp->expense_category }}


</span>


</td>





<td>

{{ $exp->description }}

</td>





<td>


<strong class="text-danger">


RM {{ number_format($exp->amount, 2) }}


</strong>


</td>





</tr>





@empty



<tr>


<td colspan="4"
class="text-center text-muted py-4">


No expense records logged yet. Fill out the form to add one!


</td>


</tr>



@endforelse





</tbody>





</table>




</div>




</div>


</div>







</div>





@endsection
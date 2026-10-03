@extends('layouts.app')


@php

$pageTitle = "Inventory Report | NEXORA";

$moduleTitle = "Inventory Report";

$moduleSubtitle = "Material Stock Analysis";

@endphp




@section('content')


<div class="page-header">

<h1>
Inventory Report
</h1>


<p>
Current material availability and stock monitoring.
</p>


</div>





<div class="section-card">


<div class="section-title">

Material Stock Report

</div>


<div class="section-subtitle mb-4">

All available raw materials

</div>





<div class="table-responsive">


<table class="table align-middle">


<thead>

<tr>

<th>
Material Name
</th>


<th>
Type
</th>


<th>
Current Stock
</th>


<th>
Minimum Stock
</th>


<th>
Status
</th>


</tr>

</thead>



<tbody>


@forelse($materials as $material)


<tr>


<td>

<strong>
{{ $material->material_name }}
</strong>

</td>



<td>

<span class="badge bg-secondary">

{{ $material->material_type }}

</span>

</td>




<td class="text-primary fw-bold">

{{ number_format($material->current_stock,2) }}
{{ $material->unit }}

</td>




<td>

{{ number_format($material->minimum_stock,2) }}
{{ $material->unit }}

</td>




<td>


@if($material->current_stock <= $material->minimum_stock)

<span class="badge bg-danger">

Low Stock

</span>


@else

<span class="badge bg-success">

Healthy

</span>


@endif


</td>



</tr>


@empty


<tr>

<td colspan="5" class="text-center py-5">

No material records found.

</td>

</tr>


@endforelse



</tbody>


</table>


</div>


</div>



@endsection
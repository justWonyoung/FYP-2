@extends('layouts.app')


@php

$pageTitle = "Inventory | Beauty Kasih";

$moduleTitle = "Inventory Management";

$moduleSubtitle = "Material Stock & Availability Monitoring";

@endphp





@section('content')





<div class="d-flex justify-content-end mb-4">



    <a href="{{ route('inventory.report') }}"
       class="btn btn-outline-primary me-2">


        <i class="bi bi-file-earmark-bar-graph me-1"></i>


        Inventory Report


    </a>






    <a href="{{ route('inventory.receiving.history') }}"
       class="btn btn-outline-secondary me-2">


        <i class="bi bi-clock-history me-1"></i>


        Receiving History


    </a>







<a href="{{ route('inventory.receive') }}"
   class="btn btn-primary">


    <i class="bi bi-box-seam me-1"></i>


    Receive Material


</a>



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
@extends('layouts.app')


@php

$pageTitle = "Receiving History | NEXORA";

$moduleTitle = "Material Receiving History";

$moduleSubtitle = "Incoming Material Transaction Records";

@endphp




@section('content')





<div class="page-header">


<h1>

Material Receiving History

</h1>



<p>

Record of all received materials and suppliers.

</p>



</div>









<div class="section-card">





<div class="section-title mb-4">

Receiving Records

</div>







<div class="table-responsive">



<table class="table align-middle">





<thead>


<tr>


<th>
DATE
</th>


<th>
MATERIAL
</th>


<th>
SUPPLIER
</th>


<th>
QUANTITY
</th>


<th>
REMARKS
</th>


</tr>


</thead>







<tbody>





@forelse($receivings as $item)





<tr>





<td>


{{ $item->received_date }}


</td>







<td>


<strong>


{{ $item->material_name }}


</strong>


</td>








<td>


{{ $item->supplier_name }}


</td>








<td>


<span class="badge bg-primary">


{{ number_format($item->quantity_received,2) }}

{{ $item->unit }}


</span>


</td>








<td>


{{ $item->remarks ?? '-' }}


</td>






</tr>







@empty




<tr>


<td colspan="5"
class="text-center text-muted py-5">


No receiving records found.


</td>


</tr>





@endforelse






</tbody>





</table>






</div>







</div>





@endsection
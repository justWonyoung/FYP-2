@extends('layouts.app')


@php

$pageTitle = "Financial Report | DBFM";

$moduleTitle = "Financial Report";

$moduleSubtitle = "Expense Analysis";

@endphp



@section('content')



<div class="page-header">


<h1>
Financial Report
</h1>


<p>
Financial expense monitoring and analysis.
</p>


</div>







<div class="row g-4 mb-4">


<div class="col-lg-4">


<div class="dashboard-card">


<div class="card-title">

Total Expenses

</div>



<div class="card-value">

RM {{ number_format($totalExpense,2) }}

</div>



<div class="stat-change">

Recorded expenses

</div>



</div>


</div>


</div>








<div class="section-card">


<div class="section-title">

Expense History

</div>



<div class="section-subtitle mb-4">

All recorded business expenses.

</div>





<div class="table-responsive">


<table class="table align-middle">


<thead>


<tr>


<th>
Category
</th>


<th>
Description
</th>


<th>
Amount
</th>


<th>
Date
</th>


</tr>


</thead>





<tbody>



@forelse($expenses as $expense)



<tr>


<td>

{{ $expense->expense_category }}

</td>




<td>

{{ $expense->description }}

</td>




<td>

RM {{ number_format($expense->amount,2) }}

</td>




<td>

{{ $expense->expense_date }}

</td>



</tr>



@empty



<tr>


<td colspan="4" class="text-center py-5">


No financial expenses recorded.


</td>


</tr>



@endforelse




</tbody>



</table>



</div>



</div>




@endsection
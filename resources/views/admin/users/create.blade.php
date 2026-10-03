@extends('layouts.app')


@php

$pageTitle = "Add User | DBFM";

$moduleTitle = "User Management";

$moduleSubtitle = "Create New Staff Account";

@endphp



@section('content')



<div class="page-header">


<h1>
Add New User
</h1>


<p>
Create a new system account and assign access role.
</p>


</div>






@if(session('success'))


<div class="alert alert-success">

{{ session('success') }}

</div>


@endif






@if($errors->any())


<div class="alert alert-danger">


<ul class="mb-0">


@foreach($errors->all() as $error)


<li>
{{ $error }}
</li>


@endforeach


</ul>


</div>


@endif







<div class="section-card">



<div class="section-title mb-4">

User Information

</div>





<form method="POST" action="{{ route('admin.users.store') }}">


@csrf





<div class="row g-4">





<div class="col-md-6">


<label class="form-label">

Full Name

</label>


<input 
type="text"
name="full_name"
class="form-control"
value="{{ old('full_name') }}"
required>


</div>






<div class="col-md-6">


<label class="form-label">

Username

</label>


<input 
type="text"
name="username"
class="form-control"
value="{{ old('username') }}"
required>


</div>








<div class="col-md-6">


<label class="form-label">

Email

</label>


<input 
type="email"
name="email"
class="form-control"
value="{{ old('email') }}"
required>


</div>







<div class="col-md-6">


<label class="form-label">

Phone Number

</label>


<input 
type="text"
name="phone_number"
class="form-control"
value="{{ old('phone_number') }}">


</div>








<div class="col-md-6">


<label class="form-label">

Role

</label>


<select 
name="role"
class="form-select"
required>


<option value="">
Select Role
</option>


<option value="admin">

Admin

</option>


<option value="staff">

Staff

</option>


<option value="finance">

Finance

</option>


</select>


</div>









<div class="col-md-6">


<label class="form-label">

Password

</label>


<input 
type="password"
name="password"
class="form-control"
required>


</div>







</div>







<div class="mt-4">


<button 
type="submit"
class="btn btn-primary">


<i class="bi bi-person-plus me-1"></i>


Create User


</button>




<a 
href="{{ route('admin.users.index') }}"
class="btn btn-outline-secondary ms-2">


Cancel


</a>


</div>






</form>




</div>





@endsection
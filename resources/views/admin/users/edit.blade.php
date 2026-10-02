@extends('layouts.app')


@php

$pageTitle = "Edit User | Beauty Kasih";

$moduleTitle = "User Management";

$moduleSubtitle = "Update Staff Account Information";

@endphp



@section('content')



<div class="page-header">


<h1>
Edit User
</h1>


<p>
Update account information and access permissions.
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







<form method="POST"
action="{{ route('admin.users.update',$user->user_id) }}">


@csrf

@method('PUT')






<div class="row g-4">






<div class="col-md-6">


<label class="form-label">

Full Name

</label>


<input 
type="text"
name="full_name"
class="form-control"
value="{{ old('full_name',$user->full_name) }}"
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
value="{{ old('username',$user->username) }}"
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
value="{{ old('email',$user->email) }}"
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
value="{{ old('phone_number',$user->phone_number) }}">


</div>









<div class="col-md-6">


<label class="form-label">

Role

</label>



<select
name="role"
class="form-select"
required>


<option value="admin"
{{ $user->role == 'admin' ? 'selected':'' }}>

Admin

</option>


<option value="staff"
{{ $user->role == 'staff' ? 'selected':'' }}>

Staff

</option>


<option value="finance"
{{ $user->role == 'finance' ? 'selected':'' }}>

Finance

</option>


</select>


</div>









<div class="col-md-6">


<label class="form-label">

Status

</label>



<select
name="status"
class="form-select"
required>


<option value="active"
{{ $user->status == 'active' ? 'selected':'' }}>

Active

</option>



<option value="inactive"
{{ $user->status == 'inactive' ? 'selected':'' }}>

Inactive

</option>



</select>


</div>









<div class="col-md-6">


<label class="form-label">

New Password

</label>


<input 
type="password"
name="password"
class="form-control">


<small class="text-muted">

Leave blank to keep current password.

</small>


</div>






</div>









<div class="mt-4">


<button
type="submit"
class="btn btn-primary">


<i class="bi bi-save me-1"></i>


Update User


</button>





<a href="{{ route('admin.users.index') }}"
class="btn btn-outline-secondary ms-2">


Cancel


</a>



</div>







</form>




</div>







@endsection
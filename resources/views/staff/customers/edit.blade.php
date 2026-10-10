@extends('layouts.app')

@php
$pageTitle = 'Edit Customer | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Maintain customer contact information';
@endphp

@section('content')
<div class="page-header"><h1>Edit Customer</h1><p>Update customer contact information.</p></div>
@include('staff.customers.form', ['action' => route('staff.customers.update', $customer), 'method' => 'PUT', 'customer' => $customer])
@endsection

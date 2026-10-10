@extends('layouts.app')

@php
$pageTitle = 'Add Customer | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Create a customer record before registering an order';
@endphp

@section('content')
<div class="page-header"><h1>Add New Customer</h1><p>Record customer contact information for order processing.</p></div>
@include('staff.customers.form', ['action' => route('staff.customers.store'), 'method' => 'POST', 'customer' => null])
@endsection

@extends('layouts.app')

@php
$pageTitle = 'Create Customer Order | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Register a customer order and its product items';
@endphp

@section('content')
<div class="page-header"><h1>Create Customer Order</h1><p>Record customer requirements before procurement and production planning.</p></div>
@include('staff.orders.form', ['action' => route('staff.orders.store'), 'method' => 'POST', 'customerOrder' => null])
@endsection

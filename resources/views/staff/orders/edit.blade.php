@extends('layouts.app')

@php
$pageTitle = 'Edit Customer Order | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Update customer order details and its production status';
@endphp

@section('content')
<div class="page-header"><h1>Edit {{ $customerOrder->order_no }}</h1><p>Update the customer, product items, and current order status.</p></div>
@include('staff.orders.form', ['action' => route('staff.orders.update', $customerOrder), 'method' => 'PUT', 'customerOrder' => $customerOrder])
@endsection

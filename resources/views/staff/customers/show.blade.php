@extends('layouts.app')

@php
$pageTitle = 'Customer Details | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Review customer information and order history';
@endphp

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div><h1>{{ $customer->customer_name }}</h1><p>Customer contact information and related orders.</p></div>
    <a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary">Back to Customers</a>
</div>

<div class="section-card mb-4">
    <div class="section-title mb-4">Customer Information</div>
    <dl class="row mb-0">
        <dt class="col-sm-3">Customer Name</dt><dd class="col-sm-9">{{ $customer->customer_name }}</dd>
        <dt class="col-sm-3">Phone Number</dt><dd class="col-sm-9">{{ $customer->phone ?: '-' }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $customer->email ?: '-' }}</dd>
        <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $customer->address ?: '-' }}</dd>
    </dl>
    <div class="mt-4"><a href="{{ route('staff.customers.edit', $customer) }}" class="btn btn-primary">Edit Customer</a></div>
</div>

<div class="section-card">
    <div class="section-title mb-4">Customer Orders</div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>ORDER NO.</th><th>ORDER DATE</th><th>STATUS</th><th>TOTAL</th><th>ACTION</th></tr></thead>
            <tbody>
            @forelse($customer->orders as $order)
                @php $badgeClass = match ($order->status) { 'pending' => 'bg-warning text-dark', 'production' => 'bg-primary', 'completed' => 'bg-success', 'cancelled' => 'bg-danger' }; @endphp
                <tr>
                    <td><strong>{{ $order->order_no }}</strong></td>
                    <td>{{ $order->order_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span></td>
                    <td>RM {{ number_format((float) $order->total_amount, 2) }}</td>
                    <td><a href="{{ route('staff.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View Order</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No customer orders have been recorded.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

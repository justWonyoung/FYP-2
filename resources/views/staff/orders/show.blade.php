@extends('layouts.app')

@php
$pageTitle = 'Customer Order '.$customerOrder->order_no.' | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'View customer order details and production planning information';
$badgeClass = match ($customerOrder->status) {
    'pending' => 'bg-warning text-dark',
    'production' => 'bg-primary',
    'completed' => 'bg-success',
    'cancelled' => 'bg-danger',
};
$priorityBadgeClass = match ($customerOrder->priority) {
    'high' => 'bg-warning text-dark',
    'urgent' => 'bg-danger',
    default => 'bg-secondary',
};
@endphp

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $customerOrder->order_no }}</h1>
        <p class="text-muted mb-0">Customer order details and ordered items.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary">Back to Customer Orders</a>
        @if($customerOrder->status !== 'cancelled')
            <a href="{{ route('staff.orders.edit', $customerOrder) }}" class="btn btn-primary">Edit Order</a>
        @endif
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="section-card h-100">
            <div class="section-title mb-4">Order Information</div>
            <dl class="row mb-0">
                <dt class="col-sm-4">Order Number</dt><dd class="col-sm-8">{{ $customerOrder->order_no }}</dd>
                <dt class="col-sm-4">Order Date</dt><dd class="col-sm-8">{{ $customerOrder->order_date->format('d M Y') }}</dd>
                <dt class="col-sm-4">Due Date</dt><dd class="col-sm-8">{{ $customerOrder->due_date?->format('d M Y') ?? '-' }}</dd>
                <dt class="col-sm-4">Priority</dt><dd class="col-sm-8"><span class="badge {{ $priorityBadgeClass }}">{{ ucfirst($customerOrder->priority ?? 'normal') }}</span></dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge {{ $badgeClass }}">{{ ucfirst($customerOrder->status) }}</span></dd>
                <dt class="col-sm-4">Total Amount</dt><dd class="col-sm-8 fw-bold">RM {{ number_format((float) $customerOrder->total_amount, 2) }}</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="section-card h-100">
            <div class="section-title mb-4">Customer Information</div>
            <dl class="row mb-0">
                <dt class="col-sm-4">Name</dt><dd class="col-sm-8">{{ $customerOrder->customer->customer_name }}</dd>
                <dt class="col-sm-4">Phone</dt><dd class="col-sm-8">{{ $customerOrder->customer->phone ?: '-' }}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $customerOrder->customer->email ?: '-' }}</dd>
                <dt class="col-sm-4">Address</dt><dd class="col-sm-8">{{ $customerOrder->customer->address ?: '-' }}</dd>
            </dl>
        </div>
    </div>
</div>

<div class="section-card">
    <div class="section-title mb-4">Ordered Items</div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>PRODUCT / ITEM</th><th class="text-end">QUANTITY</th><th class="text-end">UNIT PRICE</th><th class="text-end">SUBTOTAL</th></tr></thead>
            <tbody>
                @foreach($customerOrder->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">RM {{ number_format((float) $item->price, 2) }}</td>
                        <td class="text-end">RM {{ number_format((float) $item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot><tr><th colspan="3" class="text-end">Total Order Amount</th><th class="text-end">RM {{ number_format((float) $customerOrder->total_amount, 2) }}</th></tr></tfoot>
        </table>
    </div>
</div>
@endsection

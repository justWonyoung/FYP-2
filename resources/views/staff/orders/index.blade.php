@extends('layouts.app')

@php
$pageTitle = 'Customer Orders | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Register and track customer orders for production planning';
@endphp

@section('content')
<div class="index-page">
<header class="index-page-header">
    <div>
        <p class="index-page-eyebrow">Staff / Customer Orders</p>
        <h1>Customer Orders</h1>
        <p>Manage customer orders and track fulfilment status.</p>
    </div>
    <div class="index-page-actions">
        <a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary btn-sm">Manage Customers</a>
        <a href="{{ route('staff.orders.create') }}" class="btn btn-primary btn-sm">Create Customer Order</a>
    </div>
</header>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="section-card index-filter-card mb-4">
    <div class="index-section-heading"><div><h2 class="section-title">Find Customer Orders</h2><p class="section-subtitle">Search and filter the current order list.</p></div></div>
    <form method="GET" action="{{ route('staff.orders.index') }}" class="row g-2 align-items-end index-filter-toolbar">
        <div class="col-md-3">
            <label for="search" class="form-label">Search Order or Customer</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="e.g. CO-001 or customer name">
        </div>
        <div class="col-md-2">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach(['pending' => 'Pending', 'production' => 'Production', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="priority" class="form-label">Priority</label>
            <select id="priority" name="priority" class="form-select">
                <option value="">All priorities</option>
                @foreach(['normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="date_from" class="form-label">Date From</label>
            <input id="date_from" type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label">Date To</label>
            <input id="date_to" type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-12 d-flex flex-wrap justify-content-end gap-2">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="section-card index-list-card">
    <div class="index-section-heading mb-3"><div><h2 class="section-title">Customer Orders</h2><p class="section-subtitle">Order dates, priorities and fulfilment status.</p></div><span class="index-record-count">{{ $orders->total() }} records</span></div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>ORDER NO.</th><th>CUSTOMER</th><th>ORDER DATE</th><th>DUE DATE</th><th>PRIORITY</th><th>STATUS</th><th>TOTAL</th><th>ACTION</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                @php
                    $badgeClass = match ($order->status) {
                        'pending' => 'status-warning',
                        'production' => 'status-processing',
                        'completed' => 'status-success',
                        'cancelled' => 'status-danger',
                    };
                    $priorityBadgeClass = match ($order->priority) {
                        'high' => 'status-warning',
                        'urgent' => 'status-danger',
                        default => 'status-neutral',
                    };
                @endphp
                <tr>
                    <td><strong>{{ $order->order_no }}</strong></td>
                    <td>{{ $order->customer->customer_name }}</td>
                    <td>{{ $order->order_date->format('d M Y') }}</td>
                    <td>{{ $order->due_date?->format('d M Y') ?? '-' }}</td>
                    <td><span class="status-badge {{ $priorityBadgeClass }}">{{ ucfirst($order->priority ?? 'normal') }}</span></td>
                    <td><span class="status-badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span></td>
                    <td>RM {{ number_format((float) $order->total_amount, 2) }}</td>
                    <td class="index-row-actions">
                        <a href="{{ route('staff.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if($order->status !== 'cancelled')
                            <a href="{{ route('staff.orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No customer orders match the selected filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4 index-pagination">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
</div>
@endsection

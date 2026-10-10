@extends('layouts.app')

@php
$pageTitle = 'Customer Orders | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Register and track customer orders for production planning';
@endphp

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-4">
    <a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary">Manage Customers</a>
    <a href="{{ route('staff.orders.create') }}" class="btn btn-primary">Create Customer Order</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="section-card mb-4">
    <div class="section-title mb-3">Find Customer Orders</div>
    <form method="GET" action="{{ route('staff.orders.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
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
            <label for="date_from" class="form-label">Date From</label>
            <input id="date_from" type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label for="date_to" class="form-label">Date To</label>
            <input id="date_to" type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="section-card">
    <div class="section-title mb-4">Customer Orders</div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>ORDER NO.</th><th>CUSTOMER</th><th>ORDER DATE</th><th>STATUS</th><th>TOTAL</th><th>ACTION</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                @php
                    $badgeClass = match ($order->status) {
                        'pending' => 'bg-warning text-dark',
                        'production' => 'bg-primary',
                        'completed' => 'bg-success',
                        'cancelled' => 'bg-danger',
                    };
                @endphp
                <tr>
                    <td><strong>{{ $order->order_no }}</strong></td>
                    <td>{{ $order->customer->customer_name }}</td>
                    <td>{{ $order->order_date->format('d M Y') }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span></td>
                    <td>RM {{ number_format((float) $order->total_amount, 2) }}</td>
                    <td class="d-flex flex-wrap gap-2">
                        <a href="{{ route('staff.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                        @if($order->status !== 'cancelled')
                            <a href="{{ route('staff.orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">No customer orders match the selected filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

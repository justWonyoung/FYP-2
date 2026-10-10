@extends('layouts.app')

@php
$pageTitle = 'Customers | DBFM';
$moduleTitle = 'Customer Order Management';
$moduleSubtitle = 'Maintain customer records for order processing';
@endphp

@section('content')
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('staff.orders.index') }}" class="btn btn-outline-secondary">Customer Orders</a>
    <a href="{{ route('staff.customers.create') }}" class="btn btn-primary">Add New Customer</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<form method="GET" action="{{ route('staff.customers.index') }}" class="row g-2 mb-4">
    <div class="col-md-5">
        <label class="visually-hidden" for="search">Search customers</label>
        <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search by name, email, or phone">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Search</button>
        @if($search !== '')<a href="{{ route('staff.customers.index') }}" class="btn btn-link">Clear</a>@endif
    </div>
</form>

<div class="section-card">
    <div class="section-title mb-4">Customers</div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>CUSTOMER</th><th>PHONE</th><th>EMAIL</th><th>ADDRESS</th><th>ACTION</th></tr></thead>
            <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td><strong>{{ $customer->customer_name }}</strong></td>
                    <td>{{ $customer->phone ?: '-' }}</td>
                    <td>{{ $customer->email ?: '-' }}</td>
                    <td>{{ $customer->address ?: '-' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route('staff.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="{{ route('staff.customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('staff.customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Delete this customer? Customers with orders cannot be deleted.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5">No customers match your search.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $customers->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection

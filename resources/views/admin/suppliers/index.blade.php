@extends('layouts.app')

@php

$pageTitle = 'Supplier Management | DBFM';
$moduleTitle = 'Supplier Management';
$moduleSubtitle = 'Maintain supplier information for procurement operations';

@endphp

@section('content')

<div class="d-flex justify-content-end mb-4">

    <a href="{{ route($supplierRoutePrefix.'.suppliers.create') }}" class="btn btn-primary">
        Add New Supplier
    </a>

</div>

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif

@if(session('error'))

<div class="alert alert-danger">
    {{ session('error') }}
</div>

@endif

<form method="GET" action="{{ route($supplierRoutePrefix.'.suppliers.index') }}" class="row g-2 mb-4">
    <div class="col-md-5">
        <label class="visually-hidden" for="search">Search suppliers</label>
        <input id="search" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search by name, email, or phone">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-secondary">Search</button>
        @if($search !== '')
            <a href="{{ route($supplierRoutePrefix.'.suppliers.index') }}" class="btn btn-link">Clear</a>
        @endif
    </div>
</form>

<div class="section-card">

    <div class="section-title mb-4">
        Suppliers
    </div>

    <div class="table-responsive">

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>SUPPLIER</th>
                    <th>PHONE</th>
                    <th>EMAIL</th>
                    <th>ADDRESS</th>
                    <th>ACTION</th>
                </tr>
            </thead>

            <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td><strong>{{ $supplier->supplier_name }}</strong></td>
                    <td>{{ $supplier->phone ?? '-' }}</td>
                    <td>{{ $supplier->email ?? '-' }}</td>
                    <td>{{ $supplier->address ?? '-' }}</td>
                    <td class="text-nowrap">
                        <a href="{{ route($supplierRoutePrefix.'.suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-primary">
                            View
                        </a>

                        <a href="{{ route($supplierRoutePrefix.'.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary">
                            Edit
                        </a>

                        <form method="POST" action="{{ route($supplierRoutePrefix.'.suppliers.destroy', $supplier) }}" class="d-inline" onsubmit="return confirm('Delete this supplier? Suppliers referenced by purchase requests cannot be deleted.');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        No suppliers have been added yet.
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection

@extends('layouts.app')

@php

$pageTitle = 'Supplier Details | DBFM';
$moduleTitle = 'Supplier Management';
$moduleSubtitle = 'Review supplier contact information';

@endphp

@section('content')

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1>Supplier Details</h1>
        <p>Supplier contact information for procurement operations.</p>
    </div>
    <a href="{{ route($supplierRoutePrefix.'.suppliers.index') }}" class="btn btn-outline-secondary">Back to Suppliers</a>
</div>

<div class="section-card">
    <div class="section-title mb-4">{{ $supplier->supplier_name }}</div>

    <dl class="row mb-0">
        <dt class="col-sm-3">Phone Number</dt>
        <dd class="col-sm-9">{{ $supplier->phone ?? '-' }}</dd>

        <dt class="col-sm-3">Email</dt>
        <dd class="col-sm-9">{{ $supplier->email ?? '-' }}</dd>

        <dt class="col-sm-3">Address</dt>
        <dd class="col-sm-9">{{ $supplier->address ?? '-' }}</dd>
    </dl>

    <div class="mt-4">
        <a href="{{ route($supplierRoutePrefix.'.suppliers.edit', $supplier) }}" class="btn btn-primary">Edit Supplier</a>
    </div>
</div>

@endsection

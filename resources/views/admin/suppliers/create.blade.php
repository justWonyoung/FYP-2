@extends('layouts.app')

@php

$pageTitle = 'Add Supplier | DBFM';
$moduleTitle = 'Supplier Management';
$moduleSubtitle = 'Create supplier information for procurement operations';

@endphp

@section('content')

<div class="page-header">
    <h1>Add New Supplier</h1>
    <p>Record supplier contact information for future purchase requests.</p>
</div>

@if($errors->any())

<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>

@endif

<div class="section-card">

    <div class="section-title mb-4">
        Supplier Information
    </div>

    <form method="POST" action="{{ route($supplierRoutePrefix.'.suppliers.store') }}">
        @csrf

        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label" for="supplier_name">Supplier Name</label>
                <input id="supplier_name" type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="phone">Phone Number</label>
                <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>

            <div class="col-md-6">
                <label class="form-label" for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Create Supplier</button>
            <a href="{{ route($supplierRoutePrefix.'.suppliers.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </form>

</div>

@endsection

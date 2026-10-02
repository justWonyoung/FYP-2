@extends('layouts.app')


@php

$pageTitle = "Inventory | Beauty Kasih";
$moduleTitle = "Inventory Management";
$moduleSubtitle = "Material Stock & Availability Monitoring";

@endphp

@php

$pageTitle = 'Inventory Management';

$moduleTitle = 'Inventory Management';

$moduleSubtitle = 'Monitor raw material availability';

@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">Inventory & Material Management</h4>
    <a href="#" class="btn btn-primary">
    <i class="bi bi-box-seam me-1"></i> Receive Material
</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card stat-card p-3">
    <h6 class="m-0 fw-bold mb-3">Current Stock Levels</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>MATERIAL NAME</th>
                    <th>TYPE</th>
                    <th>CURRENT STOCK</th>
                    <th>MINIMUM STOCK</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $item)
                <tr>
                    <td><strong>{{ $item->material_name }}</strong></td>
                    <td><span class="badge bg-secondary">{{ $item->material_type }}</span></td>
                    <td><h6 class="m-0 fw-bold text-primary">{{ $item->current_stock }} {{ $item->unit }}</h6></td>
                    <td>{{ $item->minimum_stock }} {{ $item->unit }}</td>
                    <td>
                        @if($item->current_stock <= $item->minimum_stock)
                            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Low Stock</span>
                        @else
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> In Stock</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No materials registered in inventory. Click "Receive Material" to add delivered items!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
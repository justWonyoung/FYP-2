@extends('layouts.app')

@php
$pageTitle = 'Inventory | DBFM';
$moduleTitle = 'Inventory Management';
$moduleSubtitle = 'Material Stock & Availability Monitoring';
@endphp

@section('content')
<div class="index-page">
    <header class="index-page-header">
        <div><p class="index-page-eyebrow">Operations / Inventory</p><h1>Inventory</h1><p>Monitor current stock levels and material conditions.</p></div>
        <div class="index-page-actions">
            <a href="{{ route('inventory.report') }}" class="btn btn-outline-secondary btn-sm">Inventory Report</a>
            <a href="{{ route('inventory.receiving.history') }}" class="btn btn-outline-secondary btn-sm">Receiving History</a>
            @if(Auth::user()->role === 'staff')
                <a href="{{ route('inventory.receive') }}" class="btn btn-primary btn-sm">Receive Material</a>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <section class="section-card index-list-card">
        <div class="index-section-heading mb-3"><div><h2 class="section-title">Current Stock Levels</h2><p class="section-subtitle">Available material stock against configured minimum levels.</p></div><span class="index-record-count">{{ $materials->count() }} materials</span></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Material</th><th>Type</th><th>Current Stock</th><th>Minimum Stock</th><th>Unit</th><th>Stock Condition</th></tr></thead>
                <tbody>
                    @forelse($materials as $item)
                        @php
                            $isCritical = $item->current_stock <= 0;
                            $isLowStock = $item->current_stock <= $item->minimum_stock;
                        @endphp
                        <tr>
                            <td><strong>{{ $item->material_name }}</strong></td>
                            <td><span class="status-badge status-neutral">{{ $item->material_type }}</span></td>
                            <td><strong>{{ number_format($item->current_stock, 2) }}</strong></td>
                            <td>{{ number_format($item->minimum_stock, 2) }}</td>
                            <td>{{ $item->unit }}</td>
                            <td><span class="status-badge {{ $isCritical ? 'status-danger' : ($isLowStock ? 'status-warning' : 'status-success') }}">{{ $isCritical ? 'Critical' : ($isLowStock ? 'Low Stock' : 'Healthy') }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No materials registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

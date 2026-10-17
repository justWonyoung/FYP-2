@extends('layouts.app')

@php
$pageTitle = 'Production | DBFM';
$moduleTitle = 'Production Control';
$moduleSubtitle = 'Batch Processing & Yield Monitoring';
@endphp

@section('content')
<div class="index-page">
    <header class="index-page-header">
        <div><p class="index-page-eyebrow">Operations / Production</p><h1>Production</h1><p>Track production batches, output and completion progress.</p></div>
        <div class="index-page-actions">
            <a href="{{ route('production.schedule-recommendations.index') }}" class="btn btn-outline-secondary btn-sm">Scheduling Recommendation</a>
            <a href="{{ route('production.capacity.index') }}" class="btn btn-outline-secondary btn-sm">{{ Auth::user()->role === 'staff' ? 'Production Capacity' : 'View Production Capacity' }}</a>
            <a href="{{ route('production.report') }}" class="btn btn-outline-secondary btn-sm">Production Report</a>
            @if(Auth::user()->role === 'staff')
                <a href="{{ route('production.create') }}" class="btn btn-primary btn-sm">Start New Batch</a>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <section class="section-card index-list-card">
        <div class="index-section-heading mb-3"><div><h2 class="section-title">Production Batches</h2><p class="section-subtitle">Batch output, material wastage and completion progress.</p></div><span class="index-record-count">{{ $batches->count() }} batches</span></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Batch</th><th>Customer Order</th><th>Product</th><th>Planned</th><th>Actual</th><th>Yield</th><th>Wastage</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($batches as $batch)
                        @php
                            $yield = $batch->planned_output > 0 ? ($batch->actual_output / $batch->planned_output) * 100 : 0;
                            $totalWaste = $batch->wastes->sum('quantity_wasted');
                            $yieldClass = $yield >= 90 ? 'status-success' : ($yield >= 70 ? 'status-warning' : 'status-danger');
                            $statusClass = $batch->status === 'in_progress' ? 'status-processing' : 'status-success';
                        @endphp
                        <tr>
                            <td><strong>{{ $batch->batch_number }}</strong></td>
                            <td>{{ $batch->customer_order_no }}</td>
                            <td><strong>{{ $batch->product_name }}</strong></td>
                            <td>{{ number_format($batch->planned_output, 2) }} {{ $batch->unit }}</td>
                            <td>{{ number_format($batch->actual_output, 2) }} {{ $batch->unit }}</td>
                            <td><span class="status-badge {{ $yieldClass }}">{{ number_format($yield, 2) }}%</span></td>
                            <td><span class="status-badge {{ $totalWaste > 0 ? 'status-danger' : 'status-success' }}">{{ number_format($totalWaste, 2) }} wasted</span></td>
                            <td><span class="status-badge {{ $statusClass }}">{{ $batch->status === 'in_progress' ? 'In Progress' : 'Completed' }}</span></td>
                            <td class="index-row-actions">
                                @if(Auth::user()->role === 'staff')
                                    <a href="{{ route('production.log', $batch->production_id) }}" class="btn btn-outline-primary btn-sm">Log Material/Waste</a>
                                    @if($batch->status === 'in_progress')
                                        <form method="POST" action="{{ route('production.output', $batch->production_id) }}" class="index-output-form">
                                            @csrf
                                            <input type="number" step="0.01" name="actual_output" class="form-control form-control-sm" placeholder="Actual output" required>
                                            <button class="btn btn-outline-secondary btn-sm">Update</button>
                                        </form>
                                        <form method="POST" action="{{ route('production.complete', $batch->production_id) }}">
                                            @csrf
                                            <button class="btn btn-primary btn-sm" onclick="return confirm('Complete this production batch?')">Complete</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-5">No production batches created.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

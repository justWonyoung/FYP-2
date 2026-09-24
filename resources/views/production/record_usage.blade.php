@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="card stat-card p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-clipboard-data me-2"></i>Record Usage & Waste for {{ $batch->batch_number }}</h5>

        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="m-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-light p-3 rounded mb-4">
            <div class="row g-2">
                <div class="col-6"><strong>Product:</strong> {{ $batch->product_name }}</div>
                <div class="col-6"><strong>Order:</strong> {{ $batch->customer_order_no }}</div>
                <div class="col-6"><strong>Planned Target:</strong> {{ $batch->planned_output }} {{ $batch->unit }}</div>
                <div class="col-6"><strong>Status:</strong> <span class="badge bg-warning text-dark">{{ ucfirst($batch->status) }}</span></div>
            </div>
        </div>

        <form action="{{ route('production.record.store', $batch->production_id) }}" method="POST">
            @csrf
            
            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-box-arrow-right me-1"></i> 1. Material Consumption (Deducted from Inventory)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Select Material from Inventory</label>
                    <select name="material_id" class="form-select" required>
                        <option value="">-- Choose Material --</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat->material_id }}">{{ $mat->material_name }} (Available: {{ $mat->current_stock }} {{ $mat->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Quantity Consumed</label>
                    <input type="number" step="0.01" name="quantity_used" class="form-control" placeholder="10.00" required>
                </div>
            </div>

            <h6 class="fw-bold text-danger mb-3"><i class="bi bi-trash me-1"></i> 2. Material Waste Tracking (Optional)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Quantity Wasted</label>
                    <input type="number" step="0.01" name="quantity_wasted" class="form-control" placeholder="0.50">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Waste Reason / Source</label>
                    <input type="text" name="waste_reason" class="form-control" placeholder="e.g. Machine residue during mixing/filling">
                </div>
            </div>

            <h6 class="fw-bold text-success mb-3"><i class="bi bi-flag me-1"></i> 3. Batch Status & Actual Output</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Batch Status</label>
                    <select name="status" class="form-select" required>
                        <option value="in_progress" {{ $batch->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed">Complete Production Batch</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Actual Output Yield ({{ $batch->unit }})</label>
                    <input type="number" step="0.01" name="actual_output" class="form-control" value="{{ $batch->actual_output }}" placeholder="198">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save Production Logs</button>
            </div>
        </form>
    </div>
</div>
@endsection
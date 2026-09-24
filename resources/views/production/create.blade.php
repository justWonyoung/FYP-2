@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="card stat-card p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-play-circle me-2"></i>Start New Production Batch</h5>

        <form action="{{ route('production.store') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Customer Order Reference</label>
                    <input type="text" name="customer_order_no" class="form-control" placeholder="e.g. CO-089 - Siti Enterprise" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Product Name</label>
                    <input type="text" name="product_name" class="form-control" placeholder="e.g. Body Lotion 500ml" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Planned Output</label>
                    <input type="number" step="0.01" name="planned_output" class="form-control" placeholder="200" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Unit</label>
                    <select name="unit" class="form-select" required>
                        <option value="bottles">bottles</option>
                        <option value="jars">jars</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Initialize Batch</button>
            </div>
        </form>
    </div>
</div>
@endsection
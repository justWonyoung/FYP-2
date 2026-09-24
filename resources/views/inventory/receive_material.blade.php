@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="card stat-card p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-box-seam me-2"></i>Receive Delivered Material</h5>

        <form action="{{ route('receiving.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Link Approved Purchase Request (Optional)</label>
                <select name="purchase_request_id" class="form-select">
                    <option value="">-- Direct Delivery (No PR) --</option>
                    @foreach($approvedPRs as $pr)
                        <option value="{{ $pr->purchase_request_id }}">{{ $pr->request_no }} - {{ $pr->material_item }} ({{ $pr->supplier_name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Material Name</label>
                    <input type="text" name="material_name" class="form-control" placeholder="e.g. Shea Butter" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Material Type</label>
                    <select name="material_type" class="form-select" required>
                        <option value="Raw Material">Raw Material</option>
                        <option value="Packaging">Packaging</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Supplier Name</label>
                    <input type="text" name="supplier_name" class="form-control" placeholder="e.g. Azalea Chemical Sdn Bhd" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Quantity Received</label>
                    <input type="number" step="0.01" name="quantity_received" class="form-control" placeholder="50.00" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Unit</label>
                    <select name="unit" class="form-select" required>
                        <option value="kg">kg</option>
                        <option value="L">L</option>
                        <option value="pcs">pcs</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Delivery Date</label>
                <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success px-4"><i class="bi bi-download me-1"></i> Confirm & Update Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection
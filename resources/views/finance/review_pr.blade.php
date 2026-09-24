@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="card stat-card p-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2"></i>Finance Review for {{ $pr->request_no }}</h5>

        <div class="bg-light p-3 rounded mb-4">
            <div class="row g-2">
                <div class="col-6"><strong>Linked Order:</strong> {{ $pr->customer_order_no }}</div>
                <div class="col-6"><strong>Supplier:</strong> {{ $pr->supplier_name }}</div>
                <div class="col-6"><strong>Material Item:</strong> {{ $pr->material_item }}</div>
                <div class="col-6"><strong>Quantity:</strong> {{ $pr->quantity }} {{ $pr->unit }}</div>
                <div class="col-12 mt-2"><h5 class="text-primary m-0 fw-bold">Total Cost: RM {{ number_format($pr->estimated_cost, 2) }}</h5></div>
            </div>
        </div>

        <form action="{{ route('pr.finance.review', $pr->purchase_request_id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Finance Decision</label>
                <select name="finance_status" class="form-select" required>
                    <option value="approved">Approve & Forward to Admin</option>
                    <option value="rejected">Reject Purchase Request</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Finance Remarks</label>
                <textarea name="finance_remark" class="form-control" rows="3" placeholder="e.g. Approved under monthly raw material budget."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('finance.dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle me-1"></i> Submit Review</button>
            </div>
        </form>
    </div>
</div>
@endsection
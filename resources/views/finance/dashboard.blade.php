@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">Finance Review Panel</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">TOTAL REVENUE (MTD)</div>
            <h3 class="m-0 font-weight-bold mt-2 text-success">RM 87,000</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">TOTAL EXPENSE (MTD)</div>
            <h3 class="m-0 font-weight-bold mt-2 text-danger">RM 42,000</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">PENDING PR REVIEWS</div>
            <h3 class="m-0 font-weight-bold mt-2 text-warning">{{ $pendingReviews->count() }}</h3>
        </div>
    </div>
</div>

<div class="card stat-card p-3">
    <h6 class="m-0 fw-bold mb-3">Purchase Requests Awaiting Finance Review</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>PR NO</th>
                    <th>ITEM</th>
                    <th>SUPPLIER</th>
                    <th>EST. COST</th>
                    <th>FINANCE STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingReviews as $pr)
                <tr>
                    <td><strong>{{ $pr->request_no }}</strong></td>
                    <td>{{ $pr->material_item }} ({{ $pr->quantity }} {{ $pr->unit }})</td>
                    <td>{{ $pr->supplier_name }}</td>
                    <td>RM {{ number_format($pr->estimated_cost, 2) }}</td>
                    <td><span class="badge bg-warning text-dark">{{ ucfirst($pr->finance_status) }}</span></td>
                    <td>
                        <a href="{{ route('pr.finance.show', $pr->purchase_request_id) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil-square"></i> Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No pending Purchase Requests to review.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
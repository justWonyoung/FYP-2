@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-muted small">Dashboard / Overview</span>
            <h2 class="fw-bold m-0">Finance Dashboard</h2>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-4 border-success">
                <span class="text-muted small text-uppercase fw-bold">Total Revenue (MTD)</span>
                <h3 class="fw-bold text-success my-1">RM {{ number_format($totalRevenue ?? 87000, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-4 border-danger">
                <span class="text-muted small text-uppercase fw-bold">Operational Expenses</span>
                <h3 class="fw-bold text-danger my-1">RM {{ number_format($totalExpense ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-4 border-warning">
                <span class="text-muted small text-uppercase fw-bold">Approved Purchase Costs</span>
                <h3 class="fw-bold text-warning my-1">RM {{ number_format($approvedPurchaseCosts ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card p-3 border-start border-4 border-primary">
                <span class="text-muted small text-uppercase fw-bold">Estimated Net Profit</span>
                <h3 class="fw-bold text-primary my-1">RM {{ number_format($netProfit ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Pending Reviews -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold m-0">Purchase Requests Pending Finance Review</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4">PR ID</th>
                            <th>Requested By</th>
                            <th>Estimated Cost</th>
                            <th>Finance Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingReviews ?? [] as $pr)
                        <tr>
                            <td class="ps-4 fw-bold">#PR-{{ str_pad($pr->purchase_request_id ?? $pr->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $pr->staff->full_name ?? 'Staff' }}</td>
                            <td>RM {{ number_format($pr->estimated_cost ?? 0, 2) }}</td>
                            <td><span class="badge bg-warning text-dark">Pending Review</span></td>
                           <td class="text-end pe-4">
    <a
    href="{{ route('finance.purchase.show', $pr->getKey()) }}"
    class="btn btn-sm btn-primary"
>
    <i class="bi bi-eye me-1"></i>
    Review PR
</a>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No Purchase Requests awaiting Finance review.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
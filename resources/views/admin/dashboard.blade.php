@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">Admin Executive Dashboard</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">ACTIVE ORDERS</div>
            <h3 class="m-0 font-weight-bold mt-2">24</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">INVENTORY ITEMS</div>
            <h3 class="m-0 font-weight-bold mt-2">138</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">PENDING APPROVALS</div>
            <h3 class="m-0 font-weight-bold mt-2 text-danger">{{ $pendingApprovals->count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3">
            <div class="text-muted small fw-bold">MONTHLY EXPENSE</div>
            <h3 class="m-0 font-weight-bold mt-2">RM 42K</h3>
        </div>
    </div>
</div>

<div class="card stat-card p-3">
    <h6 class="m-0 fw-bold mb-3">Purchase Requests Pending Admin Final Approval</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>PR ID</th>
                    <th>ITEM</th>
                    <th>AMOUNT</th>
                    <th>FINANCE REVIEW</th>
                    <th>FINANCE REMARK</th>
                    <th>ADMIN STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingApprovals as $pr)
                <tr>
                    <td><strong>{{ $pr->request_no }}</strong></td>
                    <td>{{ $pr->material_item }} ({{ $pr->quantity }} {{ $pr->unit }})</td>
                    <td>RM {{ number_format($pr->estimated_cost, 2) }}</td>
                    <td><span class="badge bg-info text-dark">{{ ucfirst($pr->finance_status) }}</span></td>
                    <td><small class="text-muted">{{ $pr->finance_remark ?? 'None' }}</small></td>
                    <td><span class="badge bg-warning text-dark">{{ ucfirst($pr->approval_status) }}</span></td>
                    <td>
                        <form action="{{ route('admin.purchase.approve', $pr->id) }}" method="POST">
    @csrf

    <button type="submit" class="btn btn-sm btn-success">
        <i class="bi bi-check-circle"></i>
        Final Approve
    </button>

</form>
                            <i class="bi bi-check-circle"></i> Final Approve
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No Purchase Requests awaiting Admin approval.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
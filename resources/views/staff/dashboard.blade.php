@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">Staff Operational Dashboard</h4>
    <a href="{{ route('staff.pr.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Create Purchase Request</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card stat-card p-3">
    <h6 class="m-0 fw-bold mb-3">Submitted Purchase Requests</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>PR NO</th>
                    <th>ORDER NO</th>
                    <th>ITEM</th>
                    <th>SUPPLIER</th>
                    <th>COST (RM)</th>
                    <th>FINANCE REVIEW</th>
                    <th>ADMIN APPROVAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $pr)
                <tr>
                    <td><strong>{{ $pr->request_no }}</strong></td>
                    <td>{{ $pr->customer_order_no }}</td>
                    <td>{{ $pr->material_item }} ({{ $pr->quantity }} {{ $pr->unit }})</td>
                    <td>{{ $pr->supplier_name }}</td>
                    <td>RM {{ number_format($pr->estimated_cost, 2) }}</td>
                    <td><span class="badge bg-warning text-dark">{{ ucfirst($pr->finance_status) }}</span></td>
                    <td><span class="badge bg-secondary">{{ ucfirst($pr->approval_status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No Purchase Requests created yet. Click "Create Purchase Request" to add one!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
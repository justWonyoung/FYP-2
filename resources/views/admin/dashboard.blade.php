@extends('layouts.app')

@php
$pageTitle = 'Dashboard | DBFM';
$moduleTitle = 'Dashboard';
$moduleSubtitle = 'Operational Overview';
@endphp

@section('content')
<div class="admin-dashboard">
    <header class="admin-overview-header">
        <div>
            <p class="admin-overview-eyebrow">Dashboard</p>
            <h1>Operational Overview</h1>
            <p>Monitor orders, production, inventory and approvals.</p>
        </div>
        <div class="admin-overview-date">
            <span>Updated</span>
            <strong>{{ now()->format('d M Y, h:i A') }}</strong>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="row g-3 mb-4" aria-label="Operational summary">
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card admin-kpi-card">
                <div>
                    <p class="card-title">ACTIVE PRODUCTION</p>
                    <p class="card-value">{{ $activeOrders }}</p>
                    <p class="admin-kpi-note">Pending and in-progress batches</p>
                </div>
                <span class="stat-icon" aria-hidden="true"><svg class="stat-icon-svg" viewBox="0 0 24 24"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h5"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card admin-kpi-card">
                <div>
                    <p class="card-title">INVENTORY ITEMS</p>
                    <p class="card-value">{{ $totalMaterials }}</p>
                    <p class="admin-kpi-note">{{ $criticalItems > 0 ? $criticalItems.' critical item(s)' : 'Stock levels monitored' }}</p>
                </div>
                <span class="stat-icon" aria-hidden="true"><svg class="stat-icon-svg" viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/><path d="m4.5 7.8 7.5 4.2 7.5-4.2M12 12v9"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card admin-kpi-card">
                <div>
                    <p class="card-title">PENDING APPROVALS</p>
                    <p class="card-value">{{ $pendingApprovals->count() }}</p>
                    <p class="admin-kpi-note">Final Admin review required</p>
                </div>
                <span class="stat-icon stat-icon-warning" aria-hidden="true"><svg class="stat-icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card admin-kpi-card">
                <div>
                    <p class="card-title">MONTHLY EXPENSE</p>
                    <p class="card-value">RM {{ number_format($monthlyExpense, 2) }}</p>
                    <p class="admin-kpi-note">Current calendar month</p>
                </div>
                <span class="stat-icon" aria-hidden="true"><svg class="stat-icon-svg" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h.01M17 15h.01M9 12h6"/></svg></span>
            </article>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-xl-8">
            <article class="section-card admin-primary-panel h-100">
                <div class="admin-section-heading">
                    <div><h2 class="section-title">Production Overview</h2><p class="section-subtitle">Current manufacturing workload and completion state</p></div>
                    <span class="status-badge status-processing">{{ $activeOrders }} active</span>
                </div>
                <div class="admin-production-stats">
                    <div><span>Completed</span><strong>{{ $completedProduction }}</strong></div>
                    <div><span>In Progress</span><strong>{{ $runningProduction }}</strong></div>
                    <div><span>Pending</span><strong>{{ $pendingProduction }}</strong></div>
                </div>
                <div class="admin-chart-wrapper admin-chart-wrapper-primary"><canvas id="adminProductionChart" aria-label="Production status chart"></canvas></div>
            </article>
        </div>
        <div class="col-xl-4">
            <article class="section-card admin-attention-panel h-100">
                <div class="admin-section-heading"><div><h2 class="section-title">Attention Required</h2><p class="section-subtitle">Items needing operational review</p></div></div>
                <div class="admin-attention-list">
                    <div class="admin-attention-item"><span class="admin-attention-indicator admin-attention-warning"></span><div><strong>Pending approvals</strong><small>Finance-reviewed purchase requests</small></div><span class="admin-attention-count">{{ $pendingApprovals->count() }}</span></div>
                    <div class="admin-attention-item"><span class="admin-attention-indicator admin-attention-danger"></span><div><strong>Critical inventory</strong><small>Materials at zero stock</small></div><span class="admin-attention-count">{{ $criticalItems }}</span></div>
                    <div class="admin-attention-item"><span class="admin-attention-indicator admin-attention-warning"></span><div><strong>Low-stock materials</strong><small>At or below minimum stock</small></div><span class="admin-attention-count">{{ max($lowStockItems - $criticalItems, 0) }}</span></div>
                    <div class="admin-attention-item"><span class="admin-attention-indicator admin-attention-processing"></span><div><strong>Active production</strong><small>Pending and in-progress batches</small></div><span class="admin-attention-count">{{ $activeOrders }}</span></div>
                </div>
            </article>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-xl-7">
            <article class="section-card admin-chart-panel h-100">
                <div class="admin-section-heading"><div><h2 class="section-title">Customer Order Status</h2><p class="section-subtitle">Current order distribution</p></div><span class="admin-summary-total">{{ $orderSummary->sum() }} total</span></div>
                <div class="admin-chart-wrapper"><canvas id="adminOrderChart" aria-label="Customer order status chart"></canvas></div>
            </article>
        </div>
        <div class="col-xl-5">
            <article class="section-card admin-distribution-panel h-100">
                <div class="admin-section-heading"><div><h2 class="section-title">Operational Distribution</h2><p class="section-subtitle">Purchase requests and inventory health</p></div></div>
                <div class="admin-distribution-group">
                    <p class="admin-distribution-title">Purchase Requests</p>
                    <div class="admin-distribution-row"><span><i class="admin-dot admin-dot-warning"></i>Pending</span><strong>{{ $purchaseSummary->get('pending', 0) }}</strong></div>
                    <div class="admin-distribution-row"><span><i class="admin-dot admin-dot-success"></i>Approved</span><strong>{{ $purchaseSummary->get('approved', 0) }}</strong></div>
                    <div class="admin-distribution-row"><span><i class="admin-dot admin-dot-danger"></i>Rejected</span><strong>{{ $purchaseSummary->get('rejected', 0) }}</strong></div>
                </div>
                <div class="admin-distribution-group">
                    <p class="admin-distribution-title">Inventory Health</p>
                    <div class="admin-distribution-row"><span><i class="admin-dot admin-dot-success"></i>Healthy</span><strong>{{ max($totalMaterials - $lowStockItems, 0) }}</strong></div>
                    <div class="admin-distribution-row"><span><i class="admin-dot admin-dot-warning"></i>Low stock</span><strong>{{ max($lowStockItems - $criticalItems, 0) }}</strong></div>
                    <div class="admin-distribution-row"><span><i class="admin-dot admin-dot-danger"></i>Critical</span><strong>{{ $criticalItems }}</strong></div>
                </div>
            </article>
        </div>
    </section>

    <section class="section-card admin-approval-queue">
        <div class="admin-section-heading mb-3"><div><h2 class="section-title">Approval Queue</h2><p class="section-subtitle">Purchase requests requiring final administrator review</p></div><span class="admin-summary-total">{{ $pendingApprovals->count() }} awaiting review</span></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>PR ID</th><th>Item</th><th>Amount</th><th>Finance Review</th><th>Finance Remark</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($pendingApprovals as $pr)
                        <tr>
                            <td><strong>{{ $pr->request_no }}</strong></td>
                            <td>{{ $pr->material_item }}<br><small class="text-muted">{{ $pr->quantity }} {{ $pr->unit }}</small></td>
                            <td><strong>RM {{ number_format($pr->estimated_cost, 2) }}</strong></td>
                            <td><span class="status-badge {{ $pr->finance_status === 'approved' ? 'status-success' : ($pr->finance_status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ ucfirst($pr->finance_status) }}</span></td>
                            <td><small class="text-muted">{{ $pr->finance_remark ?? 'None' }}</small></td>
                            <td><span class="status-badge {{ $pr->approval_status === 'approved' ? 'status-success' : ($pr->approval_status === 'rejected' ? 'status-danger' : 'status-warning') }}">{{ ucfirst($pr->approval_status) }}</span></td>
                            <td><a href="{{ route('admin.purchase.review', $pr->getKey()) }}" class="btn btn-primary btn-sm">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No purchase requests awaiting approval.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const adminChartBase = { responsive: true, maintainAspectRatio: false, animation: { duration: 850, easing: 'easeOutQuart' }, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } }, tooltip: { padding: 10, cornerRadius: 8 } } };

new Chart(document.getElementById('adminOrderChart'), {
    type: 'doughnut',
    data: { labels: @json($orderSummary->keys()), datasets: [{ data: @json($orderSummary->values()), backgroundColor: ['#F59E0B', '#7C3AED', '#16A34A', '#DC2626'], borderWidth: 3, borderColor: '#fff' }] },
    options: adminChartBase
});

new Chart(document.getElementById('adminProductionChart'), {
    type: 'doughnut',
    data: { labels: ['Completed', 'Running', 'Pending'], datasets: [{ data: [@json($completedProduction), @json($runningProduction), @json($pendingProduction)], backgroundColor: ['#16A34A', '#7C3AED', '#F59E0B'], borderWidth: 3, borderColor: '#fff' }] },
    options: adminChartBase
});
</script>
@endpush
@endsection

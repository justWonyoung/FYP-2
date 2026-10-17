@extends('layouts.app')

@php
$pageTitle = 'Finance Dashboard | DBFM';
$moduleTitle = 'Dashboard';
$moduleSubtitle = 'Financial Overview';
@endphp

@section('content')
<div class="finance-dashboard">
    <header class="finance-overview-header">
        <div>
            <p class="finance-overview-eyebrow">Finance Dashboard</p>
            <h1>Financial Overview</h1>
            <p>Monitor revenue, expenses, purchase costs and pending financial reviews.</p>
        </div>
        <div class="finance-overview-date"><span>Updated</span><strong>{{ now()->format('d M Y, h:i A') }}</strong></div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
    @endif

    <section class="row g-3 mb-4" aria-label="Financial summary">
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card finance-kpi-card">
                <div><p class="card-title">TOTAL REVENUE</p><p class="card-value finance-value-success">RM {{ number_format($totalRevenue ?? 0, 2) }}</p><p class="finance-kpi-note">Completed customer orders</p></div>
                <span class="finance-kpi-icon finance-kpi-icon-success" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 19V5M4 19h16M8 15v-3M12 15V8M16 15v-5"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card finance-kpi-card">
                <div><p class="card-title">TOTAL EXPENSES</p><p class="card-value finance-value-danger">RM {{ number_format($totalExpense ?? 0, 2) }}</p><p class="finance-kpi-note">Recorded operational expenses</p></div>
                <span class="finance-kpi-icon finance-kpi-icon-danger" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h.01M17 15h.01M9 12h6"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card finance-kpi-card">
                <div><p class="card-title">NET PROFIT</p><p class="card-value {{ ($netProfit ?? 0) < 0 ? 'finance-value-danger' : 'finance-value-success' }}">RM {{ number_format($netProfit ?? 0, 2) }}</p><p class="finance-kpi-note">Revenue less operating expenses</p></div>
                <span class="finance-kpi-icon {{ ($netProfit ?? 0) < 0 ? 'finance-kpi-icon-danger' : 'finance-kpi-icon-success' }}" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 12h4l2-5 4 10 2-5h4"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card finance-kpi-card">
                <div><p class="card-title">APPROVED PURCHASE COST</p><p class="card-value finance-value-warning">RM {{ number_format($approvedPurchaseCosts ?? 0, 2) }}</p><p class="finance-kpi-note">Approved purchase requests</p></div>
                <span class="finance-kpi-icon finance-kpi-icon-warning" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 3h14v18H5zM8 8h8M8 12h8M8 16h5"/></svg></span>
            </article>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-xl-8">
            <article class="section-card finance-chart-panel h-100">
                <div class="finance-section-heading"><div><h2 class="section-title">Financial Summary</h2><p class="section-subtitle">Revenue, expenses, net profit and approved purchase costs</p></div></div>
                <div class="finance-chart-wrapper"><canvas id="financeDashboardChart" aria-label="Finance summary chart"></canvas></div>
            </article>
        </div>
        <div class="col-xl-4">
            <article class="section-card finance-attention-panel h-100">
                <div class="finance-section-heading"><div><h2 class="section-title">Financial Attention</h2><p class="section-subtitle">Current review and performance status</p></div></div>
                <div class="finance-attention-list">
                    <div class="finance-attention-item"><span class="finance-attention-indicator finance-indicator-warning"></span><div><strong>Pending Finance reviews</strong><small>Purchase requests awaiting action</small></div><span>{{ $pendingReviews->count() }}</span></div>
                    <div class="finance-attention-item"><span class="finance-attention-indicator {{ ($netProfit ?? 0) < 0 ? 'finance-indicator-danger' : 'finance-indicator-success' }}"></span><div><strong>Net profit status</strong><small>{{ ($netProfit ?? 0) < 0 ? 'Current loss position' : 'Current positive position' }}</small></div><span>{{ ($netProfit ?? 0) < 0 ? 'Loss' : 'Positive' }}</span></div>
                    <div class="finance-attention-item"><span class="finance-attention-indicator finance-indicator-danger"></span><div><strong>Recent expenses</strong><small>Latest recorded transactions</small></div><span>{{ $expenses->count() }}</span></div>
                    <div class="finance-attention-item"><span class="finance-attention-indicator finance-indicator-warning"></span><div><strong>Purchase cost</strong><small>Approved request commitments</small></div><span>RM {{ number_format($approvedPurchaseCosts ?? 0, 0) }}</span></div>
                </div>
            </article>
        </div>
    </section>

    <section class="section-card finance-quick-actions mb-4">
        <div class="finance-section-heading"><div><h2 class="section-title">Quick Actions</h2><p class="section-subtitle">Common Finance tasks</p></div></div>
        <div class="finance-action-list">
            <a href="{{ route('finance.expenses') }}" class="btn btn-primary btn-sm">Add Expense</a>
            <a href="{{ route('finance.report') }}" class="btn btn-outline-secondary btn-sm">View Financial Report</a>
            <a href="#pending-finance-reviews" class="btn btn-outline-secondary btn-sm">Review Purchase Requests</a>
        </div>
    </section>

    <section id="pending-finance-reviews" class="section-card finance-review-queue mb-4">
        <div class="finance-section-heading mb-3"><div><h2 class="section-title">Pending Finance Review</h2><p class="section-subtitle">Purchase requests awaiting Finance recommendation</p></div><span class="finance-summary-total">{{ $pendingReviews->count() }} pending</span></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>PR No.</th><th>Supplier</th><th>Customer Order</th><th>Estimated Cost</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($pendingReviews as $pr)
                        <tr>
                            <td><strong>{{ $pr->request_no ?? '#PR-'.str_pad($pr->purchase_request_id ?? $pr->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $pr->supplier_name ?? 'Not specified' }}</td>
                            <td>{{ $pr->customer_order_no ?? '—' }}</td>
                            <td><strong>RM {{ number_format($pr->estimated_cost ?? 0, 2) }}</strong></td>
                            <td><span class="status-badge status-warning">Pending Review</span></td>
                            <td><a href="{{ route('finance.purchase.show', $pr->getKey()) }}" class="btn btn-primary btn-sm">Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No purchase requests awaiting Finance review.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="section-card finance-recent-expenses">
        <div class="finance-section-heading mb-3"><div><h2 class="section-title">Recent Expenses</h2><p class="section-subtitle">Latest recorded operational expenses</p></div><a href="{{ route('finance.expenses') }}" class="btn btn-outline-secondary btn-sm">Manage expenses</a></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Date</th><th>Category</th><th>Description</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '—' }}</td>
                            <td>{{ $expense->expense_category }}</td>
                            <td class="finance-expense-description">{{ $expense->description }}</td>
                            <td class="text-end"><strong class="finance-value-danger">RM {{ number_format($expense->amount, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No expenses recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const financeChartFont = { family: 'Inter, sans-serif' };
const financeChartBase = {
    responsive: true,
    maintainAspectRatio: false,
    animation: { duration: 850, easing: 'easeOutQuart' },
    plugins: { legend: { display: false }, tooltip: { padding: 10, cornerRadius: 8 } },
    scales: { x: { grid: { display: false }, ticks: { font: financeChartFont, color: '#64748B' } }, y: { beginAtZero: true, grid: { color: '#E2E8F0' }, ticks: { font: financeChartFont, color: '#64748B' } } }
};

new Chart(document.getElementById('financeDashboardChart'), {
    type: 'bar',
    data: {
        labels: ['Revenue', 'Expenses', 'Net Profit', 'Purchase Costs'],
        datasets: [{
            data: [@json($totalRevenue), @json($totalExpense), @json($netProfit), @json($approvedPurchaseCosts)],
            backgroundColor: ['#16A34A', '#DC2626', @json(($netProfit ?? 0) < 0 ? '#DC2626' : '#16A34A'), '#F59E0B'],
            borderRadius: 6,
            maxBarThickness: 42
        }]
    },
    options: financeChartBase
});
</script>
@endpush
@endsection

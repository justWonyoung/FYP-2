@extends('layouts.app')

@php
$pageTitle = 'Staff Dashboard | DBFM';
$moduleTitle = 'Dashboard';
$moduleSubtitle = 'Operations Overview';
$dailyCapacity = $schedulingRecommendation['summary']['daily_capacity'];
$capacityUnit = $schedulingRecommendation['summary']['unit'] ?? 'pcs';
@endphp

@section('content')
<div class="staff-dashboard">
    <header class="staff-overview-header">
        <div>
            <p class="staff-overview-eyebrow">Staff Dashboard</p>
            <h1>Operations Overview</h1>
            <p>Track customer orders, production, inventory and daily priorities.</p>
        </div>
        <div class="staff-overview-date">
            <span>Today</span>
            <strong>{{ now()->format('d M Y') }}</strong>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="row g-3 mb-4" aria-label="Operational summary">
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card staff-kpi-card">
                <div><p class="card-title">ACTIVE CUSTOMER ORDERS</p><p class="card-value">{{ $activeCustomerOrders }}</p><p class="staff-kpi-note">Pending or in production</p></div>
                <span class="staff-kpi-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card staff-kpi-card">
                <div><p class="card-title">PENDING PURCHASE REQUESTS</p><p class="card-value">{{ $pendingRequests }}</p><p class="staff-kpi-note">Awaiting Finance or Admin review</p></div>
                <span class="staff-kpi-icon staff-kpi-icon-warning" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card staff-kpi-card">
                <div><p class="card-title">ACTIVE PRODUCTION</p><p class="card-value">{{ $activeProduction }}</p><p class="staff-kpi-note">Pending and in-progress batches</p></div>
                <span class="staff-kpi-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 20h14M7 20V9l5-5 5 5v11M10 13h4M10 17h4"/></svg></span>
            </article>
        </div>
        <div class="col-xl-3 col-md-6">
            <article class="dashboard-card staff-kpi-card">
                <div><p class="card-title">LOW / CRITICAL INVENTORY</p><p class="card-value">{{ $lowStockItems }}</p><p class="staff-kpi-note">{{ $criticalItems }} item(s) currently critical</p></div>
                <span class="staff-kpi-icon {{ $criticalItems > 0 ? 'staff-kpi-icon-danger' : 'staff-kpi-icon-warning' }}" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 4 3 20h18L12 4Z"/><path d="M12 9v5M12 17h.01"/></svg></span>
            </article>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-xl-8">
            <article class="section-card staff-plan-panel h-100">
                <div class="staff-section-heading">
                    <div><h2 class="section-title">Today's Production Plan</h2><p class="section-subtitle">Read-only recommendation based on current orders and capacity</p></div>
                    <a href="{{ route('production.schedule-recommendations.index') }}" class="btn btn-outline-secondary btn-sm">View full plan</a>
                </div>

                @if($schedulingRecommendation['warning'] !== null)
                    <div class="staff-plan-empty">{{ $schedulingRecommendation['warning'] }}</div>
                @elseif($todayPlan->isEmpty())
                    <div class="staff-plan-empty">No production is recommended for today.</div>
                @else
                    <div class="staff-plan-list">
                        @foreach($todayPlan as $recommendation)
                            <div class="staff-plan-item">
                                <div class="staff-plan-product"><strong>{{ $recommendation['product_name'] }}</strong><span>{{ $recommendation['order_no'] }}</span></div>
                                <span class="status-badge {{ $recommendation['priority'] === 'urgent' ? 'status-danger' : ($recommendation['priority'] === 'high' ? 'status-warning' : 'status-neutral') }}">{{ ucfirst($recommendation['priority']) }}</span>
                                <strong class="staff-plan-quantity">{{ number_format($recommendation['recommended_quantity'], 0) }} {{ $capacityUnit }}</strong>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($dailyCapacity !== null)
                    <div class="staff-capacity-summary">
                        <div><span>Capacity</span><strong>{{ number_format($todayPlanUsage, 0) }} / {{ number_format($dailyCapacity, 0) }} {{ $capacityUnit }}</strong></div>
                        <div class="staff-capacity-progress" aria-label="{{ round($todayPlanUtilization) }} percent capacity utilized"><span style="width: {{ $todayPlanUtilization }}%"></span></div>
                        <strong class="staff-capacity-percent">{{ round($todayPlanUtilization) }}%</strong>
                    </div>
                @endif
            </article>
        </div>
        <div class="col-xl-4">
            <article class="section-card staff-attention-panel h-100">
                <div class="staff-section-heading"><div><h2 class="section-title">Attention Required</h2><p class="section-subtitle">Operational priorities to review</p></div></div>
                <div class="staff-attention-list">
                    <div class="staff-attention-item"><span class="staff-attention-indicator staff-indicator-danger"></span><div><strong>Urgent orders</strong><small>Orders marked urgent</small></div><span>{{ $urgentOrders }}</span></div>
                    <div class="staff-attention-item"><span class="staff-attention-indicator {{ $criticalItems > 0 ? 'staff-indicator-danger' : 'staff-indicator-warning' }}"></span><div><strong>Inventory attention</strong><small>Low or critical materials</small></div><span>{{ $lowStockItems }}</span></div>
                    <div class="staff-attention-item"><span class="staff-attention-indicator staff-indicator-warning"></span><div><strong>Pending PRs</strong><small>Your requests awaiting action</small></div><span>{{ $pendingRequests }}</span></div>
                    <div class="staff-attention-item"><span class="staff-attention-indicator staff-indicator-processing"></span><div><strong>Active production</strong><small>Pending and in-progress batches</small></div><span>{{ $activeProduction }}</span></div>
                </div>
            </article>
        </div>
    </section>

    <section class="section-card staff-quick-actions mb-4">
        <div class="staff-section-heading"><div><h2 class="section-title">Quick Actions</h2><p class="section-subtitle">Common Staff operational tasks</p></div></div>
        <div class="staff-action-list">
            <a href="{{ route('staff.orders.create') }}" class="btn btn-primary btn-sm">New Customer Order</a>
            <a href="{{ route('staff.pr.create') }}" class="btn btn-outline-secondary btn-sm">Create Purchase Request</a>
            <a href="{{ route('production.create') }}" class="btn btn-outline-secondary btn-sm">Start Production</a>
            <a href="{{ route('production.schedule-recommendations.index') }}" class="btn btn-outline-secondary btn-sm">Scheduling Recommendation</a>
        </div>
    </section>

    <section class="section-card staff-active-orders">
        <div class="staff-section-heading mb-3"><div><h2 class="section-title">Active Customer Orders</h2><p class="section-subtitle">Current pending and production orders</p></div><span class="staff-summary-total">{{ $activeCustomerOrders }} active</span></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Order No.</th><th>Customer</th><th>Order Date</th><th>Due Date</th><th>Priority</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse($activeCustomerOrderList as $order)
                        @php($orderStatus = strtolower($order->status))
                        <tr>
                            <td><strong>{{ $order->order_no }}</strong></td>
                            <td>{{ $order->customer?->customer_name ?? '—' }}</td>
                            <td>{{ $order->order_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $order->due_date?->format('d M Y') ?? 'Not set' }}</td>
                            <td><span class="status-badge {{ $order->priority === 'urgent' ? 'status-danger' : ($order->priority === 'high' ? 'status-warning' : 'status-neutral') }}">{{ ucfirst($order->priority) }}</span></td>
                            <td><span class="status-badge {{ $orderStatus === 'production' || $orderStatus === 'in progress' ? 'status-processing' : 'status-warning' }}">{{ ucfirst($order->status) }}</span></td>
                            <td><a href="{{ route('staff.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No active customer orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

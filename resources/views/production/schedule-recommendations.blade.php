@extends('layouts.app')

@php
$pageTitle = 'Production Scheduling Recommendation | DBFM';
$moduleTitle = 'Production Control';
$moduleSubtitle = 'Read-only daily production planning recommendation';
@endphp

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Production Scheduling Recommendation</h1>
        <p class="text-muted mb-0">Suggested daily production allocation based on active customer orders and reported production output.</p>
    </div>
    <a href="{{ route('production.index') }}" class="btn btn-outline-secondary">Back to Production</a>
</div>

<div class="alert alert-info mb-4">
    <strong>Recommendation only.</strong> No production records are created or modified automatically.
</div>

@if($warning)
    <div class="alert alert-warning mb-4">
        {{ $warning }}
        <a href="{{ route('production.capacity.index') }}" class="alert-link">Open Production Capacity</a>.
    </div>
@else
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2"><div class="dashboard-card h-100"><div class="card-title">DAILY CAPACITY</div><div class="card-value">{{ number_format($summary['daily_capacity'], 2) }}</div><small>{{ $summary['unit'] }}</small></div></div>
        <div class="col-md-6 col-xl-2"><div class="dashboard-card h-100"><div class="card-title">ELIGIBLE ITEMS</div><div class="card-value">{{ $summary['eligible_items'] }}</div><small>Active order items</small></div></div>
        <div class="col-md-6 col-xl-2"><div class="dashboard-card h-100"><div class="card-title">REMAINING QTY</div><div class="card-value">{{ number_format($summary['total_remaining_quantity'], 2) }}</div><small>{{ $summary['unit'] }}</small></div></div>
        <div class="col-md-6 col-xl-2"><div class="dashboard-card h-100"><div class="card-title">ESTIMATED DAYS</div><div class="card-value">{{ $summary['estimated_production_days'] }}</div><small>Calendar days</small></div></div>
        <div class="col-md-6 col-xl-2"><div class="dashboard-card h-100"><div class="card-title">OVERDUE ITEMS</div><div class="card-value text-danger">{{ $summary['overdue_items'] }}</div><small>Past due date</small></div></div>
        <div class="col-md-6 col-xl-2"><div class="dashboard-card h-100"><div class="card-title">URGENT ITEMS</div><div class="card-value text-danger">{{ $summary['urgent_items'] }}</div><small>Urgent priority</small></div></div>
    </div>

    <div class="section-card mb-4">
        <div class="mb-3">
            <div class="section-title">Recommended Daily Production Plan</div>
            <div class="section-subtitle">A visual view of the read-only allocation shown in the detailed recommendation table.</div>
        </div>

        @if(count($dailyPlans) > 0)
            <div class="row g-3">
                @foreach($dailyPlans as $dailyPlan)
                    @php
                        $progressClass = $dailyPlan['utilization'] >= 100 ? 'bg-success' : 'bg-primary';
                    @endphp
                    <div class="col-12 col-lg-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <h3 class="h6 mb-1">{{ \Carbon\Carbon::parse($dailyPlan['date'])->format('d M Y') }}</h3>
                                    <small class="text-muted">Recommended production date</small>
                                </div>
                                <span class="badge {{ $dailyPlan['utilization'] >= 100 ? 'bg-success' : 'bg-secondary' }}">
                                    {{ number_format($dailyPlan['utilization'], 0) }}% utilized
                                </span>
                            </div>

                            <div class="vstack gap-2 mb-3">
                                @foreach($dailyPlan['recommendations'] as $dailyRecommendation)
                                    @php
                                        $dailyPriorityBadgeClass = match ($dailyRecommendation['priority']) {
                                            'urgent' => 'bg-danger',
                                            'high' => 'bg-warning text-dark',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <div class="bg-white border rounded-3 p-2">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div class="min-w-0">
                                                <div class="fw-semibold text-break">{{ $dailyRecommendation['product_name'] }}</div>
                                                <small class="text-muted">{{ $dailyRecommendation['order_no'] }}</small>
                                            </div>
                                            <span class="badge {{ $dailyPriorityBadgeClass }} flex-shrink-0">{{ ucfirst($dailyRecommendation['priority']) }}</span>
                                        </div>
                                        <div class="mt-2 fw-semibold">{{ number_format($dailyRecommendation['recommended_quantity'], 2) }} {{ $summary['unit'] }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Capacity</span>
                                <span class="fw-semibold">{{ number_format($dailyPlan['capacity_used'], 2) }} / {{ number_format($dailyPlan['capacity'], 2) }} {{ $summary['unit'] }}</span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Daily capacity utilization" aria-valuenow="{{ number_format($dailyPlan['utilization'], 0) }}" aria-valuemin="0" aria-valuemax="100" style="height: 8px;">
                                <div class="progress-bar {{ $progressClass }}" style="width: {{ $dailyPlan['utilization'] }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-muted">No daily production plan is available.</div>
        @endif
    </div>

    <div class="section-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
            <div>
                <div class="section-title">Recommended Daily Production</div>
                <div class="section-subtitle">Capacity is allocated sequentially from today, including weekends.</div>
            </div>
            <span class="badge bg-secondary align-self-md-start">Material Planning: Not evaluated</span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>RECOMMENDED DATE</th>
                        <th>ORDER NO.</th>
                        <th>CUSTOMER</th>
                        <th>PRODUCT</th>
                        <th>PRIORITY</th>
                        <th>DUE DATE</th>
                        <th class="text-end">QUANTITY</th>
                        <th>REASON</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recommendations as $recommendation)
                        @php
                            $priorityBadgeClass = match ($recommendation['priority']) {
                                'urgent' => 'bg-danger',
                                'high' => 'bg-warning text-dark',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($recommendation['recommended_date'])->format('d M Y') }}</td>
                            <td><strong>{{ $recommendation['order_no'] }}</strong></td>
                            <td>{{ $recommendation['customer_name'] }}</td>
                            <td>{{ $recommendation['product_name'] }}</td>
                            <td><span class="badge {{ $priorityBadgeClass }}">{{ ucfirst($recommendation['priority']) }}</span></td>
                            <td>{{ $recommendation['due_date'] ? \Carbon\Carbon::parse($recommendation['due_date'])->format('d M Y') : '-' }}</td>
                            <td class="text-end text-nowrap">
                                <div><span class="text-muted">Ordered</span> {{ number_format($recommendation['ordered_quantity'], 2) }}</div>
                                <div><span class="text-muted">Produced</span> {{ number_format($recommendation['produced_quantity'], 2) }}</div>
                                <div><span class="text-muted">Remaining</span> {{ number_format($recommendation['remaining_quantity'], 2) }}</div>
                                <div class="fw-semibold"><span class="text-muted">Plan</span> {{ number_format($recommendation['recommended_quantity'], 2) }}</div>
                                <small class="text-muted">{{ number_format($recommendation['daily_capacity_used'], 2) }} / {{ number_format($summary['daily_capacity'], 2) }} used</small>
                            </td>
                            <td class="recommendation-reason">{{ $recommendation['reason'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No eligible Customer Order items require production scheduling.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="alert alert-light border mb-0">
    <strong>Version 1 limitations:</strong> calendar days are used without holidays, shifts, machines, lines, operators, or product-specific rates. Material planning is not evaluated because no product-material BOM exists.
</div>
@endsection

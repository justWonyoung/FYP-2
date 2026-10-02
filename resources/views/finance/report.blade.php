@extends('layouts.app')


@php

$pageTitle = "Financial Report | Beauty Kasih";
$moduleTitle = "Financial Report";
$moduleSubtitle = "Profitability & Expense Analysis";

@endphp

@php

$pageTitle = 'Financial Report';

$moduleTitle = 'Financial Report';

$moduleSubtitle = 'Profitability & Expense Analysis';

@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="m-0 fw-bold">Financial Summary & Profitability Report</h4>
    <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Print / Download PDF</button>
</div>

<!-- Financial Summary KPI Cards (Figure 3.26) -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3 border-start border-4 border-success">
            <div class="text-muted small fw-bold">TOTAL REVENUE (MTD)</div>
            <h3 class="m-0 font-weight-bold mt-2 text-success">RM {{ number_format($totalRevenue, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 border-start border-4 border-danger">
            <div class="text-muted small fw-bold">OPERATIONAL EXPENSES</div>
            <h3 class="m-0 font-weight-bold mt-2 text-danger">RM {{ number_format($totalExpenses, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 border-start border-4 border-warning">
            <div class="text-muted small fw-bold">APPROVED PURCHASE COSTS</div>
            <h3 class="m-0 font-weight-bold mt-2 text-warning">RM {{ number_format($totalPurchases, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 border-start border-4 border-primary">
            <div class="text-muted small fw-bold">ESTIMATED NET PROFIT</div>
            <h3 class="m-0 font-weight-bold mt-2 text-primary">RM {{ number_format($netProfit, 2) }}</h3>
            <small class="text-muted fw-bold">Margin: {{ number_format($profitMargin, 1) }}%</small>
        </div>
    </div>
</div>

<div class="card stat-card p-4">
    <h6 class="fw-bold mb-3">Expense Breakdown by Category</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>CATEGORY</th>
                    <th>TOTAL AMOUNT</th>
                    <th>SHARE OF TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expensesByCategory as $cat => $amt)
                <tr>
                    <td><strong>{{ $cat }}</strong></td>
                    <td>RM {{ number_format($amt, 2) }}</td>
                    <td>
                        @php $pct = $grandTotalCost > 0 ? ($amt / $grandTotalCost) * 100 : 0; @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $pct }}%"></div>
                            </div>
                            <small class="fw-bold">{{ number_format($pct, 1) }}%</small>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">No financial expenses recorded for analysis.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
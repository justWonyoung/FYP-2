@extends('layouts.app')


@php

$pageTitle = "Financial Report | DBFM";

$moduleTitle = "Financial Report";

$moduleSubtitle = "Profitability & Expense Analysis";

@endphp



@section('content')



<style>


@media print {


@page {

    size:A4 portrait;

    margin:20mm;

}



body {

    background:white !important;

}



.sidebar,
.top-navbar,
.no-print,
button,
.btn {

    display:none !important;

}



.dashboard-wrapper,
.main-content,
.page-container {

    margin:0!important;

    padding:0!important;

    width:100%!important;

}



.print-report {

    width:100%!important;

    padding:10mm!important;

    border:none!important;

    box-shadow:none!important;

}



.report-card {

    break-inside:avoid;

}



.report-section {

    break-inside:avoid;

}



table {

    width:100%!important;

    font-size:11px!important;

}



th,
td {

    padding:7px!important;

}



tr {

    page-break-inside:auto;

}



}





.print-report {

background:white;

padding:35px;

border-radius:12px;

}





.report-header {

border-bottom:2px solid #ddd;

padding-bottom:15px;

margin-bottom:20px;

}





.report-company {

font-size:22px;

font-weight:800;

}





.report-title {

font-size:17px;

font-weight:700;

}





.report-date {

font-size:13px;

color:#777;

}

.print-only { display:none; }






/* SUMMARY CARD */


.report-card {


border:1px solid #ddd;

border-radius:14px;

padding:18px;

height:100%;

background:white;

}





.report-card-title {


font-size:12px;

font-weight:700;

color:#666;

letter-spacing:.5px;

}





.report-card-value {


font-size:22px;

font-weight:800;

margin-top:8px;

white-space:nowrap;

}





.report-card small {


display:block;

margin-top:8px;

color:#888;

}





.report-section {


border:1px solid #ddd;

border-radius:12px;

padding:20px;

margin-top:25px;

page-break-inside:avoid;

break-inside:avoid;


}





.report-section-title {


font-size:18px;

font-weight:700;

margin-bottom:15px;

}





.report-footer {


margin-top:25px;

padding-top:10px;

border-top:1px solid #ddd;

text-align:center;

font-size:10px;

color:#777;

page-break-inside:avoid;

break-inside:avoid;


}

@media print {

.report-footer {

    position:relative;

    margin-top:15px!important;

}

.report-section {

    margin-top:15mm!important;

}


.report-card {

    padding:12px!important;

}


.report-footer {

    margin-top:15mm!important;

}


}

@media print {
    @page { size: A4 portrait; margin: 11mm 12mm 12mm; }
    html, body { background:#fff !important; color:#172033 !important; font-size:11pt; }
    .print-report { padding:0 3mm !important; border:0 !important; border-radius:0 !important; box-shadow:none !important; }
    .print-page-one, .print-page-two { display:contents !important; }
    .report-header { padding-bottom:8mm; margin-bottom:7mm; border-bottom:2px solid #E2E8F0; }
    .report-company { font-size:19pt; color:#0F172A; }
    .report-title { margin-top:2mm; font-size:15pt; color:#5B21B6; }
    .report-date { margin-top:2mm; font-size:10pt; color:#64748B; }
    .print-only { display:block !important; }
    .report-card, .report-section { border:1px solid #E2E8F0; border-radius:8px; box-shadow:none !important; background:#fff !important; }
    .report-card { padding:4mm !important; break-inside:avoid; page-break-inside:avoid; }
    .financial-summary-chart { height:190px !important; width:100%; max-width:100%; min-width:0; padding:0 4mm 3mm; box-sizing:border-box; overflow:hidden; }
    .financial-summary-chart canvas { display:block; width:100% !important; max-width:100%; height:100% !important; }
    .report-card-title { font-size:9pt; color:#64748B; }
    .report-card-value { font-size:16pt; }
    .report-card small { font-size:8.5pt; }
    .row { break-inside:avoid; page-break-inside:avoid; }
    .report-section { margin-top:0 !important; margin-bottom:8mm; padding:5mm !important; }
    .report-section-title { margin-bottom:4mm; font-size:13pt; color:#0F172A; }
    table { width:100% !important; table-layout:fixed; font-size:9.5pt !important; }
    th, td { padding:2.5mm !important; vertical-align:top !important; overflow-wrap:anywhere; }
    th { color:#334155; background:#F8FAFC !important; }
    tr { break-inside:avoid; page-break-inside:avoid; }
    .report-section:first-of-type th:nth-child(1), .report-section:first-of-type td:nth-child(1) { width:42%; }
    .report-section:last-of-type th:nth-child(1), .report-section:last-of-type td:nth-child(1) { width:17%; }
    .report-section:last-of-type th:nth-child(2), .report-section:last-of-type td:nth-child(2) { width:22%; }
    .report-section:last-of-type th:nth-child(3), .report-section:last-of-type td:nth-child(3) { width:43%; }
    .report-footer { margin-top:12mm !important; padding-top:4mm; border-top:1px solid #CBD5E1; color:#64748B; font-size:8.5pt; break-inside:avoid; page-break-inside:avoid; }
}
</style>






<div class="d-flex justify-content-end mb-4 no-print">


<button onclick="window.print()"

class="btn btn-outline-secondary">


<i class="bi bi-printer me-1"></i>


Print / Download PDF


</button>


</div>






<div class="print-report">





<div class="report-header">


<div class="report-company">

DBFM DIGITAL MANAGEMENT SYSTEM

</div>



<div class="report-title">

Financial Performance Report

</div>



<div class="report-date">

Generated {{ now()->format('d M Y') }}

</div>

@if($dateFrom || $dateTo)
<div class="report-date print-only">Period: {{ $dateFrom?->format('d M Y') ?? 'Beginning' }} – {{ $dateTo?->format('d M Y') ?? 'Present' }}</div>
@endif

<form method="GET" action="{{ route('finance.report') }}" class="row g-2 align-items-end mb-4 no-print">
    <div class="col-md-4"><label class="form-label">Date From</label><input type="date" name="date_from" class="form-control" value="{{ $dateFrom?->toDateString() }}"></div>
    <div class="col-md-4"><label class="form-label">Date To</label><input type="date" name="date_to" class="form-control" value="{{ $dateTo?->toDateString() }}"></div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
    <div class="col-md-2"><a href="{{ route('finance.report') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
</form>


</div>






<div class="print-page print-page-one">

<!-- SUMMARY CARDS -->

<div class="row g-3">

<div class="col-12"><div class="report-card"><div class="report-card-title">FINANCIAL SUMMARY</div><div class="financial-summary-chart" style="height:240px"><canvas id="financeSummaryChart"></canvas></div></div></div>



<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

TOTAL REVENUE

</div>



<div class="report-card-value text-success">

RM {{ number_format($totalRevenue,2) }}

</div>



<small>

Monthly income tracking

</small>


</div>


</div>







<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

TOTAL EXPENSES

</div>



<div class="report-card-value text-danger">

RM {{ number_format($totalExpenses,2) }}

</div>



<small>

{{ $expenseCount }} expense records

</small>


</div>


</div>







<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

PURCHASE COST

</div>



<div class="report-card-value text-warning">

RM {{ number_format($totalPurchases,2) }}

</div>



<small>

{{ $approvedPurchaseCount }} approved requests

</small>


</div>


</div>







<div class="col-md-3">


<div class="report-card">


<div class="report-card-title">

NET PROFIT

</div>



<div class="report-card-value {{ $netProfit < 0 ? 'text-danger' : 'text-success' }}">

RM {{ number_format($netProfit,2) }}

</div>



<small>

Margin:
{{ number_format($profitMargin,2) }}%

</small>



@if($netProfit > 0)

<span class="badge bg-success mt-2">

Profitable

</span>


@else


<span class="badge bg-danger mt-2">

Loss

</span>


@endif



</div>


</div>




</div>










<!-- EXTRA SUMMARY -->


<div class="row g-3 mt-2">


<div class="col-md-6">


<div class="report-card py-3">


<div class="report-card-title">

TOTAL EXPENSE RECORDS

</div>



<div class="report-card-value">

{{ $expenseCount }}

</div>



<small>

Operational spending activities

</small>


</div>


</div>






<div class="col-md-6">


<div class="report-card py-3">


<div class="report-card-title">

APPROVED PURCHASE REQUESTS

</div>



<div class="report-card-value">

{{ $approvedPurchaseCount }}

</div>



<small>

Procurement activities completed

</small>


</div>


</div>



</div>









<!-- END PAGE ONE SUMMARY -->
</div>

<div class="print-page print-page-two">

<!-- EXPENSE BREAKDOWN -->


<div class="report-section">


<div class="report-section-title">

Expense Breakdown By Category

</div>





<table class="table table-bordered align-middle">


<thead>


<tr>


<th>

CATEGORY

</th>


<th>

TOTAL AMOUNT

</th>


<th>

SHARE

</th>


</tr>


</thead>




<tbody>



@forelse($expensesByCategory as $category=>$amount)



<tr>


<td>


<strong>

{{ $category }}

</strong>


</td>




<td>

RM {{ number_format($amount,2) }}

</td>





<td>


@if($grandTotalCost > 0)

{{ number_format(($amount/$grandTotalCost)*100,2) }}%


@else

0%


@endif



</td>


</tr>




@empty


<tr>


<td colspan="3"

class="text-center text-muted">


No expense data available.


</td>


</tr>



@endforelse




</tbody>


</table>



</div>









<!-- RECENT EXPENSE -->


<div class="report-section">


<div class="report-section-title">

Recent Expense Transactions

</div>





<table class="table table-bordered align-middle">


<thead>


<tr>


<th>

DATE

</th>


<th>

CATEGORY

</th>


<th>

DESCRIPTION

</th>


<th>

AMOUNT

</th>


</tr>


</thead>






<tbody>




@forelse($recentExpenses as $expense)




<tr>



<td>

{{ $expense->expense_date }}

</td>




<td>

{{ $expense->expense_category }}

</td>




<td>

{{ $expense->description }}

</td>




<td>


<strong class="text-danger">

RM {{ number_format($expense->amount,2) }}

</strong>


</td>




</tr>




@empty




<tr>


<td colspan="4"

class="text-center text-muted">


No expense transactions recorded.


</td>


</tr>




@endforelse




</tbody>


</table>



</div>









<div class="report-footer">


DBFM DIGITAL MANAGEMENT SYSTEM

<br>

Confidential Internal Document

<br>

Generated {{ now()->format('d M Y H:i') }}



</div>

<!-- END PAGE TWO -->
</div>







</div>





@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const financeSummaryChart = new Chart(document.getElementById('financeSummaryChart'), {
    type: 'bar',
    data: { labels: ['Revenue', 'Expenses', 'Net Profit', 'Purchase Costs'], datasets: [{ label: 'RM', data: [@json($totalRevenue), @json($totalExpenses), @json($netProfit), @json($totalPurchases)], backgroundColor: ['#16A34A', '#DC2626', @json($netProfit < 0 ? '#DC2626' : '#16A34A'), '#F59E0B'], borderRadius: 8 }] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
});

window.addEventListener('beforeprint', () => {
    const wrapper = document.querySelector('.financial-summary-chart');
    if (wrapper) wrapper.style.height = '190px';
    financeSummaryChart.resize();
});

window.addEventListener('afterprint', () => {
    const wrapper = document.querySelector('.financial-summary-chart');
    if (wrapper) wrapper.style.height = '240px';
    financeSummaryChart.resize();
});
</script>
@endpush

@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="#" class="btn btn-primary">
    <i class="bi bi-gear-wide-connected me-1"></i>
    Start New Batch
</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card stat-card p-3">
    <h6 class="m-0 fw-bold mb-3">Production Batches & Yield Performance</h6>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>BATCH NO</th>
                    <th>ORDER NO</th>
                    <th>PRODUCT</th>
                    <th>PLANNED VS ACTUAL OUTPUT</th>
                    <th>MATERIAL WASTAGE</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                <tr>
                    <td><strong>{{ $batch->batch_number }}</strong></td>
                    <td>{{ $batch->customer_order_no }}</td>
                    <td>{{ $batch->product_name }}</td>
                    <td>
                        {{ $batch->planned_output }} {{ $batch->unit }} (Planned)<br>
                        <strong class="{{ $batch->actual_output > 0 ? 'text-success' : 'text-muted' }}">
                            {{ $batch->actual_output }} {{ $batch->unit }} (Actual Yield)
                        </strong>
                    </td>
                    <td>
                        @php $totalWaste = $batch->wastes->sum('quantity_wasted'); @endphp
                        @if($totalWaste > 0)
                            <span class="badge bg-danger-subtle text-danger">{{ $totalWaste }} kg/L Wasted</span>
                        @else
                            <span class="badge bg-light text-muted">0 Wasted</span>
                        @endif
                    </td>
                    <td>
                        @if($batch->status === 'in_progress')
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> In Progress</span>
                        @else
                            <span class="badge bg-success"><i class="bi bi-check-all"></i> Completed</span>
                        @endif
                    </td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary">

    <i class="bi bi-clipboard-data"></i>
    Log Material/Waste

</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No production batches created. Click "Start New Batch" to begin!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
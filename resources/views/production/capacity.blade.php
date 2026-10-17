@extends('layouts.app')

@php
$pageTitle = 'Production Capacity | DBFM';
$moduleTitle = 'Production Control';
$moduleSubtitle = 'Daily Capacity Configuration for Scheduling Recommendations';
@endphp

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Production Capacity</h1>
        <p class="text-muted mb-0">Configure the daily capacity used by future scheduling recommendations.</p>
    </div>
    <a href="{{ route('production.index') }}" class="btn btn-outline-secondary">Back to Production</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="row g-4">
    <div class="col-lg-5">
        <div class="section-card h-100">
            <div class="section-title mb-4">Current Daily Capacity</div>
            @if($setting)
                <div class="display-6 fw-bold mb-2">{{ number_format((float) $setting->daily_capacity, 2) }} {{ $setting->unit }}</div>
                <div class="text-muted small">Last updated {{ $setting->updated_at->format('d M Y H:i') }}</div>
            @else
                <div class="text-muted">No active daily capacity has been configured.</div>
            @endif
        </div>
    </div>

    <div class="col-lg-7">
        <div class="section-card h-100">
            <div class="section-title mb-2">{{ $canEdit ? 'Update Capacity' : 'Capacity Configuration' }}</div>
            @if($canEdit)
                <p class="text-muted mb-4">This setting is used only for future scheduling recommendations. It does not modify production batches.</p>
                <form method="POST" action="{{ route('production.capacity.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label" for="daily_capacity">Daily Production Capacity</label>
                            <input id="daily_capacity" type="number" name="daily_capacity" class="form-control" min="0.01" step="0.01" value="{{ old('daily_capacity', $setting?->daily_capacity) }}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="unit">Unit</label>
                            <input id="unit" name="unit" class="form-control" maxlength="20" value="{{ old('unit', $setting?->unit ?? 'pcs') }}" required>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4">Save Capacity</button>
                </form>
            @else
                <p class="text-muted mb-0">Production capacity is read-only for Admin users.</p>
            @endif
        </div>
    </div>
</div>
@endsection

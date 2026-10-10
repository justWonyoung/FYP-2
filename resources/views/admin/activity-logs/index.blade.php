@extends('layouts.app')

@php
$pageTitle = 'Activity Log | DBFM';
$moduleTitle = 'Activity Log';
$moduleSubtitle = 'Trace important user actions across DBFM';
@endphp

@section('content')
<div class="page-header">
    <h1>Activity Log</h1>
    <p>Recent non-sensitive system activity.</p>
</div>

@if(! $auditTableAvailable)
    <div class="alert alert-warning">Activity logging will be available after the activity_logs migration is applied.</div>
@endif

<div class="section-card mb-4">
    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input id="search" name="search" value="{{ $search }}" class="form-control" placeholder="User, action, or description">
        </div>
        <div class="col-md-4">
            <label for="module" class="form-label">Module</label>
            <select id="module" name="module" class="form-select">
                <option value="">All modules</option>
                @foreach($modules as $availableModule)
                    <option value="{{ $availableModule }}" @selected($module === $availableModule)>{{ $availableModule }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="section-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>User</th><th>Module</th><th>Action</th><th>Description</th><th>Date / Time</th></tr></thead>
            <tbody>
                @forelse($activityLogs as $activityLog)
                    <tr>
                        <td>{{ $activityLog->user?->full_name ?? 'System / deleted user' }}</td>
                        <td>{{ $activityLog->module }}</td>
                        <td>{{ ucfirst($activityLog->action) }}</td>
                        <td>{{ $activityLog->description }}</td>
                        <td>{{ $activityLog->created_at?->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No activity has been recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $activityLogs->links() }}</div>
</div>
@endsection

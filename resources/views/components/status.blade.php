@props(['type' => 'neutral'])

@php
    $statusClass = match ($type) {
        'success', 'approved', 'completed', 'healthy' => 'status-success',
        'warning', 'pending', 'waiting', 'low-stock' => 'status-warning',
        'danger', 'rejected', 'cancelled', 'critical' => 'status-danger',
        'processing', 'production', 'running', 'in-progress' => 'status-processing',
        default => 'status-neutral',
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge status-badge '.$statusClass]) }}>{{ $slot }}</span>

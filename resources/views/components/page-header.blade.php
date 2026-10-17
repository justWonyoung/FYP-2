@props(['title', 'subtitle' => null, 'date' => null])

<header {{ $attributes->merge(['class' => 'page-header d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3']) }}>
    <div>
        <h1>{{ $title }}</h1>

        @if($subtitle)
            <p>{{ $subtitle }}</p>
        @endif

        @if($date)
            <div class="text-muted small mt-2">{{ $date }}</div>
        @endif
    </div>

    @isset($action)
        <div class="flex-shrink-0">{{ $action }}</div>
    @endisset
</header>

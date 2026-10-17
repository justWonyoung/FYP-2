@props(['title' => null, 'subtitle' => null])

<section {{ $attributes->merge(['class' => 'section-card']) }}>
    @if($title || isset($action))
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
            <div>
                @if($title)
                    <h2 class="section-title mb-1">{{ $title }}</h2>
                @endif

                @if($subtitle)
                    <p class="section-subtitle mb-0">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($action)
                <div class="flex-shrink-0">{{ $action }}</div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</section>

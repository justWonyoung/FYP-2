<div class="section-card">


@if(isset($title))

<div class="section-title mb-3">

{{ $title }}

</div>

@endif


{{ $slot }}


</div>
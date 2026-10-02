<div class="page-header">

    <div>

        <h1>
            {{ $title }}
        </h1>


        @if(isset($subtitle))

        <p>
            {{ $subtitle }}
        </p>

        @endif


        @if(isset($date))

        <div class="text-muted small">

            {{ $date }}

        </div>

        @endif


    </div>


    @if(isset($action))

    <div>

        {!! $action !!}

    </div>

    @endif


</div>
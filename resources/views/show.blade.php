<div class="persona-profile">
    <h1>{{ $persona->display_name ?? $persona->slug }}</h1>

    @if ($persona->headline)
        <p>{{ $persona->headline }}</p>
    @endif

    @if ($persona->bio)
        <p>{{ $persona->bio }}</p>
    @endif
</div>
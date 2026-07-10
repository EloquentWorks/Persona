@if ($layout)
    @extends($layout)
@endif

@section('content')
    <main class="persona-profile">
        @if ($persona->bannerUrl())
            <img src="{{ $persona->bannerUrl() }}" alt="{{ $persona->display_name }} banner" class="persona-profile__banner">
        @endif

        <section class="persona-profile__card">
            @if ($persona->avatarUrl())
                <img src="{{ $persona->avatarUrl() }}" alt="{{ $persona->display_name }} avatar" class="persona-profile__avatar">
            @endif

            <h1>{{ $persona->display_name ?? $user?->name ?? $persona->slug }}</h1>

            @if ($persona->headline)
                <p class="persona-profile__headline">{{ $persona->headline }}</p>
            @endif

            @if ($persona->bio)
                <p class="persona-profile__bio">{{ $persona->bio }}</p>
            @endif

            @if ($persona->location)
                <p class="persona-profile__location">{{ $persona->location }}</p>
            @endif

            @if ($persona->website_url)
                <p>
                    <a href="{{ $persona->website_url }}" rel="noopener noreferrer" target="_blank">
                        {{ $persona->website_url }}
                    </a>
                </p>
            @endif

            @if (is_array($persona->social_links))
                <ul class="persona-profile__links">
                    @foreach ($persona->social_links as $platform => $url)
                        <li>
                            <a href="{{ $url }}" rel="noopener noreferrer" target="_blank">
                                {{ ucfirst((string) $platform) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </main>
@endsection

@php
    use Illuminate\Database\Eloquent\ModelNotFoundException;

    try {
        $sponsorsPage = get_page_by_template('sponsorship', ['pages']);
    } catch (ModelNotFoundException $e) {
        $sponsorsPage = null;
    }

    $sponsorCategories = get_sponsor_categories();
@endphp
<section class="sponsorship" id="sponsorship" aria-labelledby="sponsor-title">
    <div class="container">
        <div class="reveal section-header">
            <span class="badge">{!! $sponsorsPage?->subtitle !!}</span>
            <h2 class="section-title" id="sponsor-title">{!! $sponsorsPage?->title !!}</h2>
            <div class="gold-line" aria-hidden="true"></div>
            <p class="section-sub">{!! $sponsorsPage?->content !!}</p>
        </div>

        @if (empty($sponsorCategories))
            <div class="alert alert-warning">
                <p class="alert-text">{{ __('messages.sponsors_coming_soon') }}</p>
            </div>
        @endif

        <div class="sponsors-gallery" id="sponsors-gallery">
            @foreach ($sponsorCategories as $cat)
                <div class="sponsor-cat {{ $cat['key'] }} reveal">
                    <div class="sponsor-cat-head">
                        <span class="sponsor-cat-name">{{ $cat['label'] }}</span>
                    </div>
                    <div class="sponsor-cat-body">
                        @if (empty($cat['sponsors']))
                            <p class="sponsor-soon">{{ __('messages.sponsors_category_coming_soon') }}</p>
                        @else
                            <div class="sponsor-logos">
                                @foreach ($cat['sponsors'] as $s)
                                    @if (!empty($s['url']))
                                        <a class="sponsor-logo" href="{{ $s['url'] }}" target="_blank"
                                            rel="noopener noreferrer" aria-label="{{ $s['name'] }}">
                                            <img src="{{ $s['logo'] }}" alt="{{ $s['name'] }}" loading="lazy">
                                            <span class="sponsor-logo-name">{{ $s['name'] }}</span>
                                        </a>
                                    @else
                                        <div class="sponsor-logo">
                                            <img src="{{ $s['logo'] }}" alt="{{ $s['name'] }}" loading="lazy">
                                            <span class="sponsor-logo-name">{{ $s['name'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

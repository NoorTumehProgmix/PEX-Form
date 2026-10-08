@php
    use Carbon\Carbon;
    // $main_slider is provided by PostController::home() via the "Slider" field
    // set on the CMS "home" page (Appearance > Pages > home > Slider = Hero).
    $banners = $main_slider ?? [];
    $hero = $banners[0] ?? null;

    // date in arabic
    $date = $hero['date'] ? Carbon::parse($hero['date'])->locale('ar')->translatedFormat('l d F Y') : '';
@endphp
<section class="hero" id="hero" role="banner">
    <div class="hero-bg-overlay" aria-hidden="true"></div>
    <div class="hero-bg-shapes" aria-hidden="true">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="hero-grid" aria-hidden="true"></div>

    <div class="container hero-content">
        <div class="hero-inner">
            @if ($hero['subtitle'])
                <p class="hero-kicker">{{ $hero['subtitle'] ?? '' }}</p>
            @endif
            @if ($hero['title'])
                <h1 class="hero-title">
                    {!! $hero['title'] ?? '' !!}
                </h1>
            @endif

            @if ($hero['description'])
                <p class="hero-subtitle">
                    {{ strip_editor_tags($hero['description'] ?? '') }}
                </p>
            @endif

            <div class="hero-meta" role="list">
                <div class="meta-chip" role="listitem">
                    <i class="icon-calendar1 icon"></i>
                    {{ $date }}
                </div>
                <a class="meta-chip meta-chip-link" role="listitem"
                    href="https://www.google.com/maps/search/?api=1&query=Millennium+Palestine+Ramallah" target="_blank"
                    rel="noopener noreferrer" aria-label="فتح موقع فندق الميلينيوم - رام الله على خرائط جوجل">
                    <i class="icon-pin icon"></i>
                    {{ $hero['address'] ?? '' }}
                    <i class="icon-arrow-up-right-from-square-solid-full icon"></i>
                </a>
                <div class="meta-chip" role="listitem">
                    <i class="icon-clock1 icon"></i>
                    {{ $hero['start_time'] ?? '' }}
                </div>
            </div>

            <div class="hero-actions">
                @if ($hero['register_button'])
                    <a href="{{ route('forum-registration.show') }}" class="btn-primary">
                        <!-- سجّل الآن في الملتقى -->
                        <i class="icon-user-plus-solid-full icon"></i>
                        {{ $hero['register_button'] ?? '' }}
                    </a>
                @endif
                @if ($hero['readmore_button'])
                    <a href="#about" class="btn-secondary">
                        <i class="icon-circle-exclamation-solid-full icon icon--lg"></i>
                        {{ $hero['readmore_button'] ?? '' }}
                        <!-- اعرف أكثر -->
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="countdown-wrap">
        <div class="container">
            <div class="countdown-inner" id="countdown-inner">
                <div class="countdown-units" role="timer" aria-label="العداد التنازلي للملتقى">
                    <div class="countdown-unit"><span class="countdown-num" id="cd-days">--</span><span
                            class="countdown-lbl">{{ __('messages.day') }}</span></div>
                    <div class="countdown-unit"><span class="countdown-num" id="cd-hours">--</span><span
                            class="countdown-lbl">{{ __('messages.hour') }}</span></div>
                    <div class="countdown-unit"><span class="countdown-num" id="cd-mins">--</span><span
                            class="countdown-lbl">{{ __('messages.minute') }}</span></div>
                    <div class="countdown-unit"><span class="countdown-num" id="cd-secs">--</span><span
                            class="countdown-lbl">{{ __('messages.second') }}</span></div>
                </div>
                <span class="countdown-label">{{ __('messages.until_launch') }}</span>
            </div>
        </div>
    </div>
</section>
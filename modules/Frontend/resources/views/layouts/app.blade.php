<!DOCTYPE html>
<html class="js" lang="{{ app()->getLocale() }}" dir="{{ get_direction() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ملتقى بورصة فلسطين 2026') | {{ get_config("sitename_$current_locale", 'بورصة فلسطين') }}
    </title>

    @yield('metas')

    {{-- FAVICON START --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('site.webmanifest') }}" />
    <meta name="apple-mobile-web-app-title" content="{{ get_config("sitename_$current_locale") }}">
    <meta name="application-name" content="{{ get_config("sitename_$current_locale") }}">
    {{-- FAVICON END --}}

    @php
        $loader = asset('assets/images/loader.gif');
    @endphp
    <link rel="preload" href="{{ $loader }}" as="image">

    @php
        $assetName = !empty(trim($__env->yieldContent('assetName')))
            ? trim($__env->yieldContent('assetName'))
            : 'inner';
        if (is_home()) {
            $assetName = 'home';
        }
    @endphp
    {{ Vite::useBuildDirectory('front') }}
    @vite(['modules/Frontend/resources/assets/css/' . $assetName . '.css'])
    <link rel="stylesheet" href="{{ asset('assets/fonts/icomoon/style.css') }}">

    @yield('styles')

    @if ($googleAnalytics)
        <script async src="https://www.googletagmanager.com/gtag/js" nonce="{{ csp_nonce() }}"></script>
        <script nonce="{{ csp_nonce() }}">
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', '{{ $googleAnalytics }}');
        </script>
    @endif

    @if ($fbAppId)
        <meta property="fb:app_id" content="{{ $fbAppId }}" />
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" nonce="{{ csp_nonce() }}"
            src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&autoLogAppEvents=1&version=v8.0&appId={{ $fbAppId }}">
            </script>
    @endif

    @if ($facebookPixelId)
        <script nonce="{{ csp_nonce() }}">
            ! function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $facebookPixelId }}');
            fbq('track', 'PageView');
        </script>
        <noscript nonce="{{ csp_nonce() }}"><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $facebookPixelId }}&ev=PageView&noscript=1" /></noscript>
    @endif

    @if ($bingKey)
        <meta name="msvalidate.01" content="{{ $bingKey }}" />
    @endif

    @if ($googleKey)
        <meta name="google-site-verification" content="{{ $googleKey }}" />
    @endif

    <script nonce="{{ csp_nonce() }}">
        window.csp_nonce = "{{ csp_nonce() }}";

        if (typeof window.functions === 'undefined') {
            window.functions = {};
        }

        window.functions.has_captcha = @json((bool) get_config('captcha'));
        window.functions.captcha_url = "https://www.google.com/recaptcha/api.js";
        window.functions.captcha_site_key = "{{ get_config('google_captcha.site_key') }}";
        window.validationMessages = @json(trans('validation'));
    </script>

    @yield('schemas')
</head>

<body class="@yield('bodyClass', 'pex-forum')">

    @php
        $homeUrl = route('home');
        $registerUrl = route('forum-registration.show');
    @endphp

    <header id="header" role="navigation" aria-label="القائمة الرئيسية">
        <div class="container">
            <div class="nav-inner">
                <a href="{{ $homeUrl }}" class="nav-logo" aria-label="بورصة فلسطين - Palestine Exchange">
                    <img class="nav-logo-img" src="{{ asset('assets/images/pex/pex-logo-white.png') }}"
                        alt="بورصة فلسطين - Palestine Exchange" />
                </a>
                <nav>
                    {!! $header_menu !!}
                </nav>
                <a href="{{ $registerUrl }}" class="nav-cta nav-cta-desktop">{{ __('messages.register') }}</a>

                <button class="hamburger" id="hamburger" aria-label="فتح القائمة" aria-expanded="false"
                    aria-controls="mobile-menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <nav class="mobile-menu" id="mobile-menu" aria-label="القائمة للموبايل">
        {!! $header_mobile_menu !!}
        <a href="{{ $registerUrl }}" class="mobile-cta">{{ __('messages.register') }}</a>
    </nav>

    <main class="wrapper">
        @yield('content')
    </main>

    <footer role="contentinfo">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-logo">
                    <img class="footer-logo-img" src="{{ asset('assets/images/pex/pex-logo-white.png') }}"
                        alt="بورصة فلسطين - Palestine Exchange" />
                </div>
                <p class="footer-copyright">{{ __('messages.copyright') }}</p>
                {!! $sub_footer !!}
            </div>
        </div>
    </footer>

    @stack('scripts')
    @vite(['modules/Frontend/resources/assets/js/' . $assetName . '.js'])
</body>

</html>
<!DOCTYPE html>
<html lang="en" data-kit-theme="default">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">

    <title>{{ @$title }}</title>
    <link rel="icon" type="image/png" href="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('backend_assets/images/favicon.ico') }}" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />

    {{ Vite::useBuildDirectory('build') }}
    @vite('modules/Backend/resources/assets/css/app.css')


    @include('cms::components.juzaweb_langs')

    @if (get_config('captcha'))
        <script nonce="{{ csp_nonce() }}" src="https://www.google.com/recaptcha/api.js"></script>
    @endif

    @yield('header')

</head>

<body class="juzaweb__layout--cardsShadow juzaweb__menuLeft--dark">
    <div class="juzaweb__layout juzaweb__layout--hasSider">
        <div class="juzaweb__menuLeft__backdrop"></div>
        <div class="juzaweb__layout">
            <div id="jquery-message"></div>

            @yield('content')

            @if (get_config('captcha'))
                <div id="recaptcha-render"></div>
            @endif
        </div>
    </div>
    @stack('scripts')
</body>

</html>

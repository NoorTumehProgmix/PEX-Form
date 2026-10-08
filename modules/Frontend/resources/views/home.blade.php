@extends('frontend::layouts.app')

@section('assetName', 'home')
@section('bodyClass', 'pex-forum')
@section('title', 'ملتقى بورصة فلسطين 2026 — شركاء نحو تطوير قطاع الأوراق المالية الفلسطيني')

@section('metas')
    <meta name="description"
        content="الملتقى السنوي لبورصة فلسطين — شركاء نحو تطوير قطاع الأوراق المالية الفلسطيني. الثلاثاء 15 سبتمبر 2026، فندق الميلينيوم – رام الله. سجّل الآن." />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="ملتقى بورصة فلسطين 2026" />
    <meta property="og:title" content="ملتقى بورصة فلسطين 2026 — شركاء نحو تطوير قطاع الأوراق المالية الفلسطيني" />
    <meta property="og:description"
        content="الملتقى السنوي لبورصة فلسطين. الثلاثاء 15 سبتمبر 2026 — فندق الميلينيوم، رام الله. سجّل الآن." />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:image" content="{{ asset('assets/images/og-image.png') }}" />
    <meta property="og:locale" content="ar_AR" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="ملتقى بورصة فلسطين 2026" />
    <meta name="twitter:description" content="شركاء نحو تطوير قطاع الأوراق المالية الفلسطيني — 15 سبتمبر 2026، رام الله." />
    <meta name="twitter:image" content="{{ asset('assets/images/og-image.png') }}" />
@endsection
@section('content')
    @include('frontend::templates.homepage.pex.hero')
    @include('frontend::templates.homepage.pex.about')
    @include('frontend::templates.homepage.pex.speakers')
    @include('frontend::templates.homepage.pex.agenda')
    @include('frontend::templates.homepage.pex.sponsorship')
    @include('frontend::templates.homepage.pex.register')
    @include('frontend::templates.homepage.pex.contact')
    @include('frontend::templates.homepage.pex.partials.profile-modal')
@endsection
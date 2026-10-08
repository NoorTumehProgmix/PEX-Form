@extends('frontend::layouts.app')

@section('assetName', 'register')
@section('bodyClass', 'pex-forum pex-register-page')
@section('title', 'التسجيل في الملتقى — ملتقى بورصة فلسطين 2026')

@section('metas')
    <meta name="description"
        content="سجّل للمشاركة في الملتقى السنوي لبورصة فلسطين 2026 — شركاء نحو تطوير قطاع الأوراق المالية الفلسطيني." />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="ملتقى بورصة فلسطين 2026" />
    <meta property="og:title" content="التسجيل في الملتقى — ملتقى بورصة فلسطين 2026" />
    <meta property="og:description" content="نموذج التسجيل للمشاركة في الملتقى السنوي لبورصة فلسطين 2026." />
    <meta property="og:url" content="{{ route('forum-registration.show') }}" />
    <meta property="og:image" content="{{ asset('assets/images/og-image.png') }}" />
    <meta property="og:locale" content="ar_AR" />
@endsection

@section('content')
    @include('frontend::templates.homepage.pex.register-page')
@endsection

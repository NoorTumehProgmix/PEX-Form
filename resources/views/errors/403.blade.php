@extends('frontend::layouts.app')
@section('title', __('exceptions.403'))
@section('meta_description', __('exceptions.403'))
@section('metas')
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self';script-src 'self'; style-src 'self'; img-src 'self';font-src 'self';connect-src 'self';frame-src 'self'; object-src 'none'; media-src 'self'; frame-ancestors 'none'; base-uri 'self';form-action 'self';">
@endsection
@section('content')
    <div class="inner-page">
        @include('frontend::partials.inner_header', ['title' => __('exceptions.403')])
        <div class="inner-body">
            <div class="container">
                <div class="inner-body-content">
                    <div class="error-wrap text-center">
                        <div class="error-content">
                            <div class="error-page">
                                <h1 class="error-title">403</h1>
                            </div>
                            <div class="error-desc">
                                <p>{{ __('exceptions.403') }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

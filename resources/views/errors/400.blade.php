@extends('frontend::layouts.app')
@section('title', __('exceptions.400'))
@section('meta_description', __('exceptions.400'))
@section('meta')
    <meta name="robots" content="noindex, nofollow">
@endsection
@section('content')
    <div class="inner-page">
        @include('frontend::partials.inner_header', ['title' => __('exceptions.400')])
        <div class="inner-body">
            <div class="container">
                <div class="inner-body-content">
                    <div class="error-wrap text-center">
                        <div class="error-content">
                            <div class="error-page">
                                <h1 class="error-title">400</h1>
                            </div>
                            <div class="error-desc">
                                <p>{{ __('exceptions.400') }}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

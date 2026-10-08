@php
    $pageTitle = (isset($title)
            ? $title
            : $main_post->getMeta('meta_heading'))
        ? $main_post->getMeta('meta_heading')
        : $main_post->title;
@endphp

<div class="inner-header {{ isset($classes) ? $classes : '' }}">
    <h1 class="inner-header-title"> {!! $pageTitle !!}</h1>
    {{-- @include('frontend::partials.inner.breadcrumb') --}}
    @if (isset($main_post) && !empty($main_post->subtitle) && isset($showSubtitle) && $showSubtitle)
        <h2 class="inner-header-subtitle">{{ $main_post->subtitle }}</h2>
    @endif
</div>

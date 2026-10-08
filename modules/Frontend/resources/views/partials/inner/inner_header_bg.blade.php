@php
    $bgImage = '';
    if (isset($main_post) && !empty($main_post->getMeta('background_image'))) {
        $bgImage = upload_url($main_post->getMeta('background_image'), null, size: '2500x1500');
    }
    if (empty($bgImage) and !empty(get_config('banner'))) {
        $bgImage = upload_url(get_config('banner'), null, size: '2500x1500');
    }
@endphp
@if (!empty($bgImage))
    <div class="inner-header-bg lazyload" data-bg="{{ $bgImage }}"></div>
@endif

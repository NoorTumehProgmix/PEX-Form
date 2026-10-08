<meta name="description" content="{!! $metas['meta_description'] !!}">
<meta name="keywords" content="{!! implode(', ', $metas['meta_keywords']) !!}">
<meta name="author" content="{!! $metas['meta_author'] !!}">
<link rel="canonical" href="{{ url()->current() }}" />
<meta name="robots"
    content="{{ $metas['meta_showRobots'] == 'yes' ? 'index, follow' : 'noindex, nofollow' }}@if (!empty($metas['meta_robots']) && $metas['meta_robots'] != 'noindex') , {{ $metas['meta_robots'] }} @endif">
<meta name="copyright" content="{{ $metas['meta_copyright'] }}">
{{-- facebook --}}
<meta property="og:site_name" content="{!! $metas['meta_og_site_name'] !!}" />
<meta property="og:title" content="{!! $metas['meta_og_title'] !!}">
<meta property="og:type" content="{{ $metas['meta_og_type'] }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:image" content="{{ url(upload_url($metas['meta_og_image'], null, '1200x630', 100, false)) }}" />
{{--  --}}
<meta property="og:description" content="{!! $metas['meta_og_description'] !!}">
{{-- twitter --}}
<meta name="twitter:card" content="{{ $metas['meta_twitter_card'] }}">
<meta property="twitter:title" content="{!! $metas['meta_twitter_title'] !!}">
<meta property="twitter:description" content="{!! $metas['meta_twitter_description'] !!}">
<meta name="twitter:image" content="{{ url(upload_url($metas['meta_twitter_image'], null, '1200x675', 100, false)) }}">
{{--  --}}
<meta name="twitter:image:alt" content="{{ $metas['meta_twitter_image_alt'] }}">{{--  --}}
{{--
**for addin or editing the metas or metas default values**
modules\CMS\Traits\ResourceController.php private function setMetas ,
config/app.php metas (defaults value if the metas empty),
modules\Backend\resources\views\backend\items\seo_form.blade.php (cruid form),
modules\Frontend\resources\views\components\meta\meta.blade.php,
--}}

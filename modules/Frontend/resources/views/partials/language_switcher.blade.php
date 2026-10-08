@foreach ($locales as $local_key => $locale_name)
    @if ($local_key === $current_locale)
        @continue
    @endif

    @php
        $defaultLocale = get_config('language') ?? config('app.fallback_locale');

        if (! array_key_exists($defaultLocale, $locales)) {
            $defaultLocale = config('app.fallback_locale');
        }
        $routeParams = $local_key === $defaultLocale ? [] : ['locale' => $local_key];
        $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
        $navLink = '';
        if (isset($main_post) || isset($albums) || isset($events)) {
            if (isset($albums)) {
                $routeParams['slug'] = $main_slug;
            } elseif (isset($events) || $main_post->slug == 'homepage') {
                unset($routeParams['slug']);
            } else {
                $routeParams['slug'] = path_link($main_post['path']);
            }
        }
        if (Route::has($currentRoute)) {
            $navLink = route($currentRoute, $routeParams);
        }
    @endphp

    <li><a href="{{ $navLink }}" title="{{ $locale_name['name'] }}" class="item lang">{{ $locale_name['key'] }}</a>
    </li>
@endforeach

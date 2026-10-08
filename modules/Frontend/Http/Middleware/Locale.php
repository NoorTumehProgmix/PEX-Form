<?php

namespace Juzaweb\Frontend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locales = array_keys(config('app.locales', []));
        $defaultLocale = get_config('language') ?? config('app.fallback_locale');

        if (! in_array($defaultLocale, $locales, true)) {
            $defaultLocale = config('app.fallback_locale');
        }
        $lang = $request->segment(1);

        if (in_array($lang, $locales, true)) {
            if ($lang === $defaultLocale) {
                $segments = $request->segments();
                array_shift($segments);
                $path = implode('/', $segments);
                $query = $request->getQueryString();
                $redirectUrl = $path . ($query ? '?' . $query : '');

                return redirect($redirectUrl, 301);
            }

            app()->setLocale($lang);
            URL::defaults(['locale' => $lang]);
            Session::put('current_locale', $lang);
        } else {
            app()->setLocale($defaultLocale);
            URL::defaults(['locale' => $defaultLocale]);
            Session::put('current_locale', $defaultLocale);
        }

        return $next($request);
    }
}

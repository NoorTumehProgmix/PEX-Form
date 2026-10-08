<?php

namespace Juzaweb\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableSessionForBots
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ua = $request->userAgent() ?? '';

        if (stripos($ua, 'Googlebot') !== false) {
            // Just mark the request
            $request->attributes->set('is_bot', true);
            config([
                'session.driver' => 'array',
            ]);
            view()->share('errors', new \Illuminate\Support\ViewErrorBag());
        }


        return $next($request);
    }
}

<?php

namespace Juzaweb\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ShareErrorsUnlessBot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->attributes->get('is_bot')) {
            // Skip sharing errors (no session exists)
            return $next($request);
        }

        return app(ShareErrorsFromSession::class)->handle($request, $next);
    }
}

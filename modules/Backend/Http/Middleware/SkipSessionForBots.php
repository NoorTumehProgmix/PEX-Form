<?php

namespace Juzaweb\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Session\Middleware\StartSession;

class SkipSessionForBots
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->attributes->get('is_bot')) {
            // Do NOT start session
            return $next($request);
        }

        return app(StartSession::class)->handle($request, $next);
    }
}

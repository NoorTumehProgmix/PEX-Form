<?php

namespace Juzaweb\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Progmix\Restrictions\Models\Restriction;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (pluginActive("progmix/restrictions")) {
         // Get the client's IP address
         $ip = $request->ip();

         // Check if the IP is in the blocked IPs table
         $blockedIp = Restriction::where('ip', $ip)->first();
         // If the IP is blocked, deny access
         if ($blockedIp) {
             abort(403, 'Your IP is blocked from accessing this admin page.');
         }
        }

        return $next($request);
    }
}

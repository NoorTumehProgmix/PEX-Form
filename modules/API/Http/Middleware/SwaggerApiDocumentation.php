<?php

namespace Juzaweb\API\Http\Middleware;

class SwaggerApiDocumentation
{
    public function handle($request, \Closure $next)
    {
        if (!config('juzaweb.api.enable')) {
            abort(404);
        }

        return $next($request);
    }
}

<?php

namespace Juzaweb\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XssSanitization
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
        $input = $request->except(['g-recaptcha-response', '_token']);

        array_walk_recursive($input, function (&$value, $key) use ($request) {
            $pattern = '/\b(SELECT\s+|INSERT\s+INTO|UPDATE\s+|DELETE\s+|DROP\s+TABLE|CREATE\s+TABLE|ALTER\s+TABLE|UNION\s+|--|\/\*|\*\/|SLEEP|BENCHMARK)\b/i';

            if (preg_match($pattern, $value, $matches)) {
                $matchedWord = $matches[1] ?? $matches[0];

                Log::warning('Invalid input detected from XssSanitization middleware', [
                    'field'     => $key,
                    'input'     => $value,
                    'matched'   => $matchedWord,
                    'url'       => $request->fullUrl(),
                ]);

                abort(422, 'Invalid input detected');
            }

            // Sanitize input
            $value = strip_tags($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $value = addslashes($value);
        });
        $request->merge($input);
        return $next($request);
    }
}

<?php

namespace Juzaweb\Backend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PathTraversal
{
    protected $patterns = [
        '../',             // Basic path traversal
        '..\\',            // Windows style path traversal
        '%2f',             // URL encoded slash
        '%5c',             // URL encoded backslash
        '%2e%2e%2f',       // URL encoded path traversal
        '%2e%2e\\',        // Windows URL encoded
        '%c0%af',          // Unicode encoded slash
        '%252e%252e%252f', // Double URL encoded
        '%2e%2e%5c',       // Another Windows URL encoded
        '/',               // Slash
        '\\',              // Backslash
        '%2e',             // URL encoded dot
        '%252e',           // Double URL encoded dot
        '%c0%ae',          // UTF-8 encoded dot
        './',              // Single dot with slash
        '.\\',             // Single dot with backslash
        '..%00/',          // Null byte with slash
        '%u002e',          // Unicode dot
        '%u2215',          // Unicode division slash
        '%uff0e',          // Fullwidth full stop
        '%uff3c',          // Fullwidth reverse solidus
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->except(['search', 'slug', 'date_of_birth', 'date_of_issue', 'date_of_expiry', 'signature', 'request_token', 'utf8', 'g-recaptcha-response', '_token']);
        foreach ($input as $key => $value) {
            // Only check non-null string values
            if (is_string($value)) {
                $this->checkForPathTraversal($key, $value);
            }

            // Check if it's an uploaded file and validate the original filename
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $this->checkForPathTraversal($key, $value->getClientOriginalName());
            }
        }

        return $next($request);
    }

    /**
     * Check the given value for path traversal patterns.
     *
     * @param  string  $value
     * @return void
     */
    protected function checkForPathTraversal($key, string $value): void
    {
        foreach ($this->patterns as $pattern) {
            if (stripos($value, $pattern) !== false) {
                Log::info('Invalid input detected from NoPathTraversal rule: ', [
                    'input' => $value,
                    'url'      => request()->fullUrl(),
                    'matched'  => $pattern,
                ]);
                abort(422, 'Invalid input detected due to path traversal.');
            }
        }
    }
}

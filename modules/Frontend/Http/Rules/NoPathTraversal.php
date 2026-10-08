<?php

namespace Juzaweb\Frontend\Http\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class NoPathTraversal implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check for common path traversal patterns
        $patterns = [
            'request',
            '../',        // Basic path traversal
            '..\\',       // Windows style path traversal
            '%2f',        // URL encoded slash
            '%5c',        // URL encoded backslash
            '%2e%2e%2f',  // URL encoded path traversal
            '%2e%2e\\',   // Windows URL encoded
            '%c0%af',     // Unicode encoded slash
            '%252e%252e%252f', // Double URL encoded
            '%2e%2e%5c',  // Another Windows URL encoded
            // '/',          // Slash
            // '\\',         // Backslash
            '%2e',        // URL encoded dot
            '%252e',      // Double URL encoded dot
            '%c0%ae',     // UTF-8 encoded dot
            './',         // Single dot with slash
            // '.\\',        // Single dot with backslash
            '..%00/',     // Null byte with slash
            '%u002e',     // Unicode dot
            '%u2215',     // Unicode division slash
            '%uff0e',     // Fullwidth full stop
            '%uff3c',     // Fullwidth reverse solidus
        ];

        // Handle both strings and file uploads
        if ($value instanceof UploadedFile) {
            $check_value = $value->getClientOriginalName();
        } else {
            $check_value = $value;
        }

        foreach ($patterns as $pattern) {
            if (stripos($check_value, $pattern) !== false) {
                $fail('Invalid input detected');
            }
        }
    }
}

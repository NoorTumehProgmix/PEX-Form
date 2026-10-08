<?php

namespace Juzaweb\Frontend\Http\Requests\Concerns;

use Closure;
use Illuminate\Support\Facades\Http;

trait ValidatesCaptcha
{
    /**
     * Minimum reCAPTCHA v3 score (0.0–1.0). Google recommends 0.5 as a starting threshold.
     */
    protected function captchaMinScore(): float
    {
        return 0.5;
    }

    /**
     * Expected reCAPTCHA v3 action name for this form submission.
     */
    protected function captchaExpectedAction(): string
    {
        return 'submit';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function captchaRules(): array
    {
        if (! get_config('captcha')) {
            return [];
        }

        return [
            'g-recaptcha-response' => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $g_response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => get_config('google_captcha.secret_key'),
                        'response' => $value,
                        'remoteip' => request()->ip(),
                    ]);

                    if (! $g_response->successful()) {
                        $fail(__('validation.recaptcha'));

                        return;
                    }

                    $body = $g_response->json();

                    if (! is_array($body) || empty($body['success'])) {
                        $fail(__('validation.recaptcha'));

                        return;
                    }

                    $score = $body['score'] ?? null;

                    if (! is_numeric($score) || (float) $score < $this->captchaMinScore()) {
                        $fail(__('validation.recaptcha'));

                        return;
                    }

                    if (($body['action'] ?? '') !== $this->captchaExpectedAction()) {
                        $fail(__('validation.recaptcha'));
                    }
                },
            ],
        ];
    }
}

<?php



namespace Juzaweb\CMS\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Closure;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'email' => 'bail|required|email|max:150',
            'password' => 'bail|required|min:6|max:32',
        ];

        if (get_config('captcha')) {
            $rules['g-recaptcha-response'] = ['required', function (string $attribute, mixed $value, Closure $fail) {
                $g_response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => get_config('google_captcha.secret_key'),
                    'response' => $value,
                    'remoteip' => request()->ip()
                ]);
                if (!$g_response->json()['success']) {
                    $fail('Captcha is not valid');
                }
            }];
        }

        return $rules;
    }
}

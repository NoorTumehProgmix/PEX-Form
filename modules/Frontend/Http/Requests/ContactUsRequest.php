<?php

namespace Juzaweb\Frontend\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Juzaweb\Frontend\Http\Rules\NoPathTraversal;

class ContactUsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            '_token'       => [new NoPathTraversal],
            'name'         => ['required', 'string', 'max:191', new NoPathTraversal],
            'email'   => ['required', 'email', 'max:191', new NoPathTraversal],
            'phone'   => [
                'required',
                'regex:/^(?:(?:(\+?972|\(\+?972\)|\+?\(972\)|\+?970|\(\+?970\)|\+?\(970\))(?:\s|\.|-)?([1-9]\d?))|(0[23489]{1})|(0[57]{1}[0-9]))(?:\s|\.|-)?([^0\D]{1}\d{2}(?:\s|\.|-)?\d{4})$/',
                'min:8',
                'max:20',
                new NoPathTraversal
            ],
            'message' => ['required', 'string', 'max:191', new NoPathTraversal],
        ];

        if (get_config('captcha')) {
            $rules['g-recaptcha-response'] = [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    $g_response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret'   => get_config('google_captcha.secret_key'),
                        'response' => $value,
                        'remoteip' => request()->ip()
                    ]);
                    if (!$g_response->json()['success']) {
                        $fail('Captcha is not valid');
                    }
                }
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required'     => __('messages.required'),
            'name.maxlength'    => __('messages.maxlength', ['attribute' => 191]),
            'email.required'    => __('messages.required'),
            'email.email'       => __('messages.email'),
            'email.max'         => __('messages.maxlength', ['attribute' => 191]),
            'phone.required'    => __('messages.required'),
            'phone.regex'       => __('messages.wrong_phone'),
            'phone.min'         => __('messages.minlength', ['attribute' => 8]),
            'phone.max'         => __('messages.maxlength', ['attribute' => 20]),
            'message.required'  => __('messages.required'),
            'message.maxlength' => __('messages.maxlength', ['attribute' => 191]),
        ];
    }
}

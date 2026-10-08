<?php

namespace Juzaweb\Frontend\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\Rule;
use Juzaweb\Frontend\Http\Requests\Concerns\ValidatesCaptcha;
use Juzaweb\Frontend\Http\Rules\NoPathTraversal;

class ForumRegistrationRequest extends FormRequest
{
    use ValidatesCaptcha;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email') && is_string($this->input('email'))) {
            $this->merge([
                'email' => strtolower(trim($this->input('email'))),
            ]);
        }

        $token = $this->header('X-CSRF-TOKEN') ?: $this->input('_token');

        if (! is_string($token) || ! hash_equals($this->session()->token(), $token)) {
            throw new TokenMismatchException('CSRF token mismatch.');
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $partTypes = ['حضور', 'متحدث', 'راعٍ', 'شريك استراتيجي'];
        $sponsorTypes = ['راعٍ ماسي', 'راعٍ ذهبي', 'راعٍ فضي'];

        return array_merge([
            'name' => ['required', 'string', 'max:191', new NoPathTraversal],
            'institution' => ['required', 'string', 'max:191', new NoPathTraversal],
            'job_title' => ['nullable', 'string', 'max:191', new NoPathTraversal],
            'email' => [
                'required',
                'email',
                'max:191',
                Rule::unique('forum_registrations', 'email'),
                new NoPathTraversal,
            ],
            'phone' => [
                'required',
                'regex:/^(?:\+|00)?(?:970|972|0)?[\s\-]?5[69][\s\-]?\d{3}[\s\-]?\d{4}$/',
                'min:8',
                'max:20',
                new NoPathTraversal,
            ],
            'part_type' => ['required', 'string', Rule::in($partTypes), new NoPathTraversal],
            'sponsor_type' => [
                Rule::requiredIf(fn () => $this->input('part_type') === 'راعٍ'),
                'nullable',
                'string',
                Rule::in($sponsorTypes),
                new NoPathTraversal,
            ],
        ], $this->captchaRules());
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.max' => __('validation.maxlength', ['attribute' => 191]),
            'institution.required' => __('validation.required'),
            'institution.max' => __('validation.maxlength', ['attribute' => 191]),
            'job_title.max' => __('validation.maxlength', ['attribute' => 191]),
            'email.required' => __('validation.required'),
            'email.email' => __('validation.email'),
            'email.max' => __('validation.maxlength', ['attribute' => 191]),
            'email.unique' => __('validation.unique_email'),
            'phone.regex' => __('validation.wrong_phone'),
            'phone.min' => __('validation.minlength', ['attribute' => 8]),
            'phone.max' => __('validation.maxlength', ['attribute' => 20]),
            'part_type.required' => __('validation.required'),
            'part_type.in' => __('validation.required'),
            'sponsor_type.required' => __('validation.required'),
            'sponsor_type.in' => __('validation.required'),
            'g-recaptcha-response.required' => __('validation.recaptcha'),
        ];
    }
}

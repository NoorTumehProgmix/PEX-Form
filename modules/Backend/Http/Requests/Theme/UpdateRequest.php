<?php


namespace Juzaweb\Backend\Http\Requests\Theme;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'theme' => [
                'bail',
                'string',
                'required',
            ]
        ];
    }
}

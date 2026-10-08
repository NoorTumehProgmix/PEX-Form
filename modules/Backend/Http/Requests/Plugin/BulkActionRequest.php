<?php

namespace Juzaweb\Backend\Http\Requests\Plugin;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'action' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'ids' => trans_cms('tadcms::app.plugins'),
            'action' => trans_cms('tadcms::app.action'),
        ];
    }
}

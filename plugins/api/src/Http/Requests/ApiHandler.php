<?php

namespace Progmix\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiHandler extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'api_id' => 'required|exists:awap_apis,id',
            'request' => 'nullable|json',
            'response' => 'nullable|json|max:4096', // Adjust the max size as per your requirement
            'type' => 'nullable|in:client/edge,edge/origin',
            'ip' => 'nullable|ip',
            'status_code' => 'nullable|integer',
            'duration' => 'nullable|integer',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
            'attempt_id' => 'required|integer',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}

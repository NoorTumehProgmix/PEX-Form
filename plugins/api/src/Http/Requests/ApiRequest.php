<?php

namespace Progmix\Api\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $rules = [
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'status' => 'required|in:1,0',
            'maintenance' => 'required|in:1,0',
            'method' => 'required|in:GET,POST,DELETE,PUT,PATCH',
            'origin_url' => 'required|string',
            'response_keys.*' => 'nullable|string|max:255|required_with:response_values.*',
            'response_values.*' => 'nullable|string|max:255|required_with:response_keys.*',
            'response_friendly.*' => 'nullable|string|max:255|required_with:response_keys.*',
            'header_keys.*' => 'nullable|string|max:255|required_with:header_values.*',
            'header_values.*' => 'nullable|string|max:255|required_with:header_keys.*',
            'param_keys.*' => 'nullable|string|max:255|required_with:param_values.*',
            'param_values.*' => 'nullable|string|max:255',
            'query_keys.*' => 'nullable|string|max:255|required_with:query_values.*',
            'query_values.*' => 'nullable|string|max:255|required_with:query_keys.*',
            'body_keys.*' => 'nullable|string|max:255|required_with:body_values.*',
            'body_values.*' => 'nullable|string|max:255|required_with:body_keys.*',
            'message' => 'required|string',
            'description' => 'nullable|string',
            'timeout' => 'nullable|integer',
            'timeout_message' => 'nullable|string|max:255',

        ];

        // Add unique rule only for creation
        if ($this->api?->id == null) {
            $rules['edge_name'] = [
                'required',
                'string',
                'max:255',
                'unique:awap_apis,edge_name'
            ];
        } else {
            $rules['edge_name'] =  [
                'required',
                'string',
                'max:255',
                Rule::unique('awap_apis', 'edge_name')->ignore($this->api?->id)
            ];
        }

        return $rules;
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

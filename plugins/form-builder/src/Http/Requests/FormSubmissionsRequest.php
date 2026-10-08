<?php

namespace Progmix\FormBuilder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Progmix\FormBuilder\Models\Form;
use Progmix\FormBuilder\Rules\onlyAvailableItemsValidator;
use Progmix\FormBuilder\Rules\uniqueJsonValidator;
use Spatie\TranslationLoader\LanguageLine;

class FormSubmissionsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $form = Form::find($this->dynamicCode['id']);

        foreach ($this->submission['data'] as $key => $value) {
            $this->merge([$key => $value]);
        }

        return (array)json_decode($form->validations);
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
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        $customMessages = [];

        foreach ($this->rules() as $fieldName => $rules) {

            $form = Form::findOrFail($this->dynamicCode['id']);
            $formDefinition = json_decode($form->form_definition, true);
            $label = '';
            try {
                foreach ($formDefinition as $element) {
                    if (isset($element['key']) && $element['key'] ===  $fieldName) {
                        $label  = $element['label'];
                        break;
                    }
                }
                $lang = LanguageLine::where('key', $label)->firstOrFail();
                $label = $lang->text[app()->getLocale()];
            } catch (\Exception $e) {
                $label = $fieldName;
            }

            foreach ($rules as $rule) {
                $value = substr($rule, strpos($rule, ':') + 1);
                if (strpos($rule, 'onlyAvailableItems') !== false) {
                    $customMessages[$fieldName . '.only_available_items'] =  trans_cms('formBuilder::content.the_field') . ' "' . $label . '" ' . trans_cms('formBuilder::content.must_be_valid') . '.';
                }
                if (strpos($rule, 'maxWords') !== false) {
                    $customMessages[$fieldName . '.max_words'] = trans_cms('formBuilder::content.the_field') . ' "' . $label . trans_cms('formBuilder::content.cannot_exceed') . '" ' . $value . trans_cms('formBuilder::content.words') . '.';
                }
                if (strpos($rule, 'minWords') !== false) {
                    $customMessages[$fieldName . '.min_words'] = trans_cms('formBuilder::content.the_field') . ' "' . $label . trans_cms('formBuilder::content.cannot_be_less_than') . '" ' . $value . trans_cms('formBuilder::content.words') . '.';
                }
                if (strpos($rule, 'uniqueJson') !== false) {
                    $customMessages[$fieldName . '.unique_json'] = trans_cms('formBuilder::content.the_field') . ' "' . $label . '" ' . trans_cms('formBuilder::content.must_be_unique') . '.';
                }
            }
        }

        return $customMessages;
    }
}

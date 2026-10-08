<?php

namespace Progmix\FormBuilder\Rules;

use Illuminate\Contracts\Validation\Rule;
use Progmix\FormBuilder\Models\Form;
use Spatie\TranslationLoader\LanguageLine;

class onlyAvailableItemsValidator implements Rule
{
    protected $formId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($formId)
    {
        $this->formId = $formId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $form = Form::findOrFail($this->formId);
        $formDefinition = json_decode($form->form_definition, true);

        foreach ($formDefinition as $element) {

            if (isset($element['key']) && $element['key'] ===  $attribute) {
                $validValues = $element['values'];
            }
        }

        $validValueKeys = array_column($validValues, 'value');
        $valueKeys = array_keys($value);
        return empty(array_diff($valueKeys, $validValueKeys));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The field  :attribute  must be valid.";
    }
}

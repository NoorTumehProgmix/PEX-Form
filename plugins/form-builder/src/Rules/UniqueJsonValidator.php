<?php

namespace Progmix\FormBuilder\Rules;

use Google\Service\Dfareporting\Resource\Languages;
use Illuminate\Contracts\Validation\Rule;
use Progmix\FormBuilder\Models\Form;
use Progmix\FormBuilder\Models\FormSubmission;
use Spatie\TranslationLoader\LanguageLine;

class uniqueJsonValidator implements Rule
{

    protected $index;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($index)
    {
        $this->index = $index;
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
        $forms = FormSubmission::where('form_id', $this->index)->get();
        foreach ($forms as $form) {
            $formData = json_decode($form->form_data, true);
            if (is_array($formData) && isset($formData['data'][$attribute])) {
                if ($formData['data'][$attribute] == $value)
                    return false;
            }
        }

        return true;;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The field :attribute must be unique within the JSON array.";
    }
}

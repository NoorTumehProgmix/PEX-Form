<?php

namespace Progmix\FormBuilder\Rules;

use Illuminate\Contracts\Validation\Rule;

class minWordsValidator implements Rule
{
    protected $minWords;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($minWords)
    {
        $this->minWords = $minWords;
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
        $wordCount = str_word_count($value);
        return $wordCount <= $this->minWords;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute cannot be less than {$this->minWords} words.";
    }
}

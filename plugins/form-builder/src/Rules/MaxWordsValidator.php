<?php

namespace Progmix\FormBuilder\Rules;

use Illuminate\Contracts\Validation\Rule;

class maxWordsValidator implements Rule
{
    protected $maxWords;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($maxWords)
    {
        $this->maxWords = $maxWords;
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
        return $wordCount <= $this->maxWords;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute cannot exceed {$this->maxWords} words.";
    }
}

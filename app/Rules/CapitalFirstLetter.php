<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CapitalFirstLetter implements ValidationRule
{
    public function __construct(
            private string $message
    )
    {
    }
    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (mb_convert_case($value, MB_CASE_TITLE_SIMPLE) !== $value) {
            $fail($this->message)->translate();
        }
    }
}

<?php

namespace App\ValueObjects;

use Illuminate\Validation\ValidationException;

readonly final class PersonName
{
    public string $name;
    
    private function __construct(?string $name, string $validatableAttribute, string $translationStringKey)
    {
        if (!$name || mb_convert_case($name, MB_CASE_TITLE_SIMPLE) !== $name) {
            throw ValidationException::withMessages([
                $validatableAttribute => trans($translationStringKey)
            ]);
        }
        
        $this->name = $name;
    }
    
    public static function create(?string $name, string $validatableAttribute, string $translationStringKey): self
    {
        return new self($name, $validatableAttribute, $translationStringKey);
    }
}

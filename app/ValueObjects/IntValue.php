<?php

namespace App\ValueObjects;

use Illuminate\Validation\ValidationException;

readonly class IntValue
{
    public int $value;
    
    private function __construct(?string $value, string $validatableAttribute, string $translationStringKey)
    {
        if(!intval($value)) {
            throw ValidationException::withMessages([
                $validatableAttribute => trans($translationStringKey)
            ]);
        }
        
        $this->value = $value;
    }
    
    public static function create(?string $value, string $validatableAttribute, string $translationStringKey): self
    {
        return new self($value, $validatableAttribute, $translationStringKey);
    }
}

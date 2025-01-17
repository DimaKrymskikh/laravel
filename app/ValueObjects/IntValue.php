<?php

namespace App\ValueObjects;

use Illuminate\Validation\ValidationException;

final readonly class IntValue
{
    public int $value;
    
    private function __construct(?string $value, ?string $validatableAttribute = null, ?string $translationStringKey = null)
    {
        $intValue = intval(trim($value ?? ''));
        
        if(!$intValue) {
            if ($validatableAttribute && $translationStringKey) {
                throw ValidationException::withMessages([
                    $validatableAttribute => trans($translationStringKey)
                ]);
            } else {
                $this->value = 0;
            }
            
            return;
        }
        
        $this->value = $value;
    }
    
    public static function create(?string $value, ?string $validatableAttribute = null, ?string $translationStringKey = null): self
    {
        return new self($value, $validatableAttribute, $translationStringKey);
    }
}

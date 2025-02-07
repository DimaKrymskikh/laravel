<?php

namespace App\ValueObjects;

use App\Exceptions\RuleException;

final readonly class IntValue
{
    public int $value;
    
    private function __construct(?string $value, ?string $attribute = null, ?string $message = null)
    {
        $strValue = trim($value ?? '');
        
        // intval может вернуть не ноль.
        // Например, '17dd' -> 17
        $regValue = preg_match('/^-*[0-9]+$/', $strValue);
        
        if (!$regValue) {
            if ($attribute && $message) {
                throw new RuleException($attribute, $message);
            } else {
                $this->value = 0;
            }
            
            return;
        }
        
        $this->value = intval($strValue);
    }
    
    public static function create(?string $value, ?string $attribute = null, ?string $message = null): self
    {
        return new self($value, $attribute, $message);
    }
}

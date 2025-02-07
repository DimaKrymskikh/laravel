<?php

namespace App\ValueObjects;

use App\Exceptions\RuleException;

final readonly class PersonName
{
    public string $name;
    
    private function __construct(?string $name, string $attribute, string $message)
    {
        if (!$name || mb_convert_case($name, MB_CASE_TITLE_SIMPLE) !== $name) {
            throw new RuleException($attribute, $message);
        }
        
        $this->name = $name;
    }
    
    public static function create(?string $name, string $attribute, string $message): self
    {
        return new self($name, $attribute, $message);
    }
}

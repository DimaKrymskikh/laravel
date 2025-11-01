<?php

namespace App\ValueObjects\ScalarTypes;

final readonly class SimpleStringValue
{
    public string $value;
    
    private function __construct(string|null $value)
    {
        $this->value = $value ?? '';
    }
    
    public static function create(string|null $value): self
    {
        return new self($value);
    }
}

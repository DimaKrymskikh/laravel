<?php

namespace App\ValueObjects\ScalarTypes;

final readonly class BoolValue {
    public bool $value;
    
    private function __construct(mixed $value)
    {
        if ($value === 'false') {
            $this->value = false;
            return;
        }
        
        if ($value) {
            $this->value = true;
        } else {
            $this->value = false;
        }
    }
    
    public static function create(mixed $value): self
    {
        return new self($value);
    }
}

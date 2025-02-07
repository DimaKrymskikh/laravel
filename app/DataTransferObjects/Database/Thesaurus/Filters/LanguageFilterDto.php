<?php

namespace App\DataTransferObjects\Database\Thesaurus\Filters;

final readonly class LanguageFilterDto
{
    public string $name;
    
    public function __construct(?string $name)
    {
        $this->name = trim($name ?? '');
    }
}

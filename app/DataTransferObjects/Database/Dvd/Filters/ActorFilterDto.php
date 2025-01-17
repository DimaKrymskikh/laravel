<?php

namespace App\DataTransferObjects\Database\Dvd\Filters;

final readonly class ActorFilterDto
{
    public string $name;
    
    public function __construct(?string $name)
    {
        $this->name = trim($name ?? '');
    }
}

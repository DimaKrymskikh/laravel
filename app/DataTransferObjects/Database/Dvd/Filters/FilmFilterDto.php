<?php

namespace App\DataTransferObjects\Database\Dvd\Filters;

final readonly class FilmFilterDto
{
    public string $title;
    public string $description;
    public string $releaseYear;
    
    public function __construct(?string $title, ?string $description, ?string $releaseYear)
    {
        $this->title = trim($title ?? '');
        $this->description = trim($description ?? '');
        $this->releaseYear = trim($releaseYear ?? '');
    }
}

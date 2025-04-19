<?php

namespace App\DataTransferObjects\Database\Dvd\Filters;

final readonly class FilmFilterDto
{
    public string $title;
    public string $description;
    public string $releaseYear;
    public string $languageName;
    
    public function __construct(string|null $title, string|null $description, string|null $releaseYear, string|null $languageName)
    {
        $this->title = trim($title ?? '');
        $this->description = trim($description ?? '');
        $this->releaseYear = trim($releaseYear ?? '');
        $this->languageName = trim($languageName ?? '');
    }
}

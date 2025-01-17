<?php

namespace App\DataTransferObjects\Database\Dvd;

use App\ValueObjects\IntValue;

final readonly class FilmDto
{
    public string $title;
    public string $description;
    public IntValue $releaseYear;

    public function __construct(?string $title, ?string $description, IntValue $releaseYear)
    {
        $this->title = trim($title ?? '');
        $this->description = trim($description ?? '');
        $this->releaseYear = $releaseYear;
    }
}

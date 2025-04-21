<?php

namespace App\Modifiers\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Models\Dvd\Film;

interface FilmModifiersInterface
{
    public function save(Film $film, FilmDto $dto): void;
    
    public function saveField(Film $film, string $field, string|null $value): void;
    
    public function delete(int $filmId): void;
}

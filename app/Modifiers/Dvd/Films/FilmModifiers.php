<?php

namespace App\Modifiers\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use Illuminate\Support\Facades\DB;

class FilmModifiers implements FilmModifiersInterface
{
    public function save(Film $film, FilmDto $dto): void
    {
        $film->title = $dto->title;
        $film->description = $dto->description;
        $film->release_year = $dto->releaseYear->value;
        $film->save();
    }
    
    public function saveField(Film $film, string $field, string|null $value): void
    {
        $film->$field = $value;
        $film->save();
    }
    
    public function delete(int $filmId): void
    {
        DB::transaction(function () use ($filmId) {
            FilmActor::where('film_id', $filmId)->delete();
            Film::where('id', $filmId)->delete();
        });
    }
}

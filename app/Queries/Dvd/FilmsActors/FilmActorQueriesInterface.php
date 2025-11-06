<?php

namespace App\Queries\Dvd\FilmsActors;

use App\Services\Database\Dvd\Dto\FilmActorDto;
use Illuminate\Database\Eloquent\Collection;

interface FilmActorQueriesInterface
{
    public function exists(FilmActorDto $dto): bool;
    
    public function getByFilmId(int $filmId): Collection;
}

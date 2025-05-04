<?php

namespace App\Queries\Dvd\FilmsActors;

use Illuminate\Database\Eloquent\Collection;

interface FilmActorQueriesInterface
{
    public function exists(int $filmId, int $actorId): bool;
    
    public function getByFilmId(int $filmId): Collection;
}

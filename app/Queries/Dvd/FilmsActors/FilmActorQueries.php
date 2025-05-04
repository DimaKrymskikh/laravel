<?php

namespace App\Queries\Dvd\FilmsActors;

use App\Models\Dvd\FilmActor;
use Illuminate\Database\Eloquent\Collection;

final class FilmActorQueries implements FilmActorQueriesInterface
{
    public function exists(int $filmId, int $actorId): bool
    {
        return FilmActor::where('film_id', $filmId)
                ->where('actor_id', $actorId)
                ->exists();
    }
    
    public function getByFilmId(int $filmId): Collection
    {
        return FilmActor::where('film_id', $filmId)->get();
    }
}

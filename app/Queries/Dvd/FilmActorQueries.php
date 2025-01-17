<?php

namespace App\Queries\Dvd;

use App\Models\Dvd\FilmActor;
use Illuminate\Database\Eloquent\Builder;

final class FilmActorQueries
{
    public function checkFilmActor(int $filmId, int $actorId): bool
    {
        return FilmActor::where('film_id', '=', $filmId)
                ->where('actor_id', '=', $actorId)
                ->exists();
    }
    
    public function queryFilmActor(int $filmId, int $actorId): Builder
    {
        return FilmActor::where('actor_id', $actorId)
                ->where('film_id', $filmId);
    }
    
    public function queryActorsListByFilmId(int $filmId): Builder
    {
        return FilmActor::where('film_id', $filmId);
    }
}

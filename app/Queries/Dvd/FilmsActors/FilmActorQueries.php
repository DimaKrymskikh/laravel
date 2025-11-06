<?php

namespace App\Queries\Dvd\FilmsActors;

use App\Models\Dvd\FilmActor;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use Illuminate\Database\Eloquent\Collection;

final class FilmActorQueries implements FilmActorQueriesInterface
{
    public function exists(FilmActorDto $dto): bool
    {
        return FilmActor::where('film_id', $dto->filmId)
                ->where('actor_id', $dto->actorId)
                ->exists();
    }
    
    public function getByFilmId(int $filmId): Collection
    {
        return FilmActor::where('film_id', $filmId)->get();
    }
}

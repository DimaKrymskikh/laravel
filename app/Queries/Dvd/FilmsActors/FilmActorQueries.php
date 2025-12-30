<?php

namespace App\Queries\Dvd\FilmsActors;

use App\Models\Dvd\FilmActor;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Support\Collections\Dvd\FilmActorCollection;

final class FilmActorQueries implements FilmActorQueriesInterface
{
    public function exists(FilmActorDto $dto): bool
    {
        return FilmActor::where('film_id', $dto->filmId)
                ->where('actor_id', $dto->actorId)
                ->exists();
    }
    
    public function getByFilmId(int $filmId): FilmActorCollection
    {
        return FilmActor::where('film_id', $filmId)->get();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getListInLazy(\Closure $callback): void
    {
        FilmActor::select('film_id', 'actor_id')
                ->lazy(self::NUMBER_OF_ITEMS_IN_CHUNCK)
                ->each($callback);
    }
}

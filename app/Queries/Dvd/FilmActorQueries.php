<?php

namespace App\Queries\Dvd;

use App\Models\Dvd\FilmActor;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Services\DatabaseQueryInterface;
use App\Support\Collections\Dvd\FilmActorCollection;

class FilmActorQueries implements DatabaseQueryInterface
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 200;
    
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
     * Извлекает по частям все данные таблицы 'dvd.films_actors'.
     * Используется метод 'lazy'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazy(\Closure $callback): void
    {
        FilmActor::select('film_id', 'actor_id')
                ->lazy(self::NUMBER_OF_ITEMS_IN_CHUNCK)
                ->each($callback);
    }
}

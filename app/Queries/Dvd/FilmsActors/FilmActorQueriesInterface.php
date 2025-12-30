<?php

namespace App\Queries\Dvd\FilmsActors;

use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Support\Collections\Dvd\FilmActorCollection;

interface FilmActorQueriesInterface
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 200;
    
    public function exists(FilmActorDto $dto): bool;
    
    public function getByFilmId(int $filmId): FilmActorCollection;
    
    /**
     * Извлекает по частям все данные таблицы 'dvd.films_actors'.
     * Используется метод 'lazy'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazy(\Closure $callback): void;
}

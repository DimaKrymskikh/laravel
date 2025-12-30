<?php

namespace App\Queries\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Queries\QueriesInterface;
use App\Support\Collections\Dvd\FilmCollection;

interface FilmQueriesInterface extends QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'dvd.films' нет записи с id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 25;
    
    /**
     * При заданном фильтре возвращает число элементов в таблице dvd.films
     * 
     * @param FilmFilterDto $dto
     * @return int
     */
    public function count(FilmFilterDto $dto): int;
    
    /**
     * При заданном фильтре возвращает коллекцию элементов из таблицы dvd.films
     * с количественным ограничением 
     * 
     * @param FilmFilterDto $dto
     * @return FilmCollection
     */
    public function getListWithFilter(FilmFilterDto $dto): FilmCollection;
    
    /**
     * Жадная загрузка фильмов с актёрами
     * 
     * @param int $id
     * @return Film
     */
    public function getByIdWithActors(int $id): Film;
    
    public function getNumberInTableByIdWithOrderByTitle(int $id): int|null;
    
    /**
     * Извлекает по частям все данные таблицы 'dvd.films'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void;
}

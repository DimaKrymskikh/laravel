<?php

namespace App\Queries\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Queries\SimpleQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface FilmQueriesInterface extends SimpleQueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'dvd.films' нет записи с id=%d";
    
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
     * @return Collection
     */
    public function getList(FilmFilterDto $dto): Collection;
    
    /**
     * Жадная загрузка фильмов с актёрами
     * 
     * @param int $id
     * @return Film
     */
    public function getByIdWithActors(int $id): Film;
    
    /**
     * При заданной сортировке возвращает коллекцию, в которой id и порядковый номер n,
     * определяемый сортировкой
     * 
     * @return Collection
     */
    public function getRowNumbers(): Collection;
}

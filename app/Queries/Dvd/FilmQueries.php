<?php

namespace App\Queries\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Providers\AppServiceProvider;
use App\Queries\QueriesInterface;
use App\Support\Collections\Dvd\FilmCollection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FilmQueries implements QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'dvd.films' нет записи с id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 25;
    
    public function exists(int $id): bool
    {
        return Film::where('id', $id)->exists();
    }
    
    public function getById(int $id): Film
    {
        return Film::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * Возвращает порядковый номер фильма (элемент таблицы 'dvd.films')
     * с id = $id при сортировке по названию фильма.
     * 
     * @param int $id
     * @return int|null
     */
    public function getNumberInTableByIdWithOrderByTitle(int $id): int|null
    {
        return DB::scalar(<<<SQL
                    SELECT 
                        n
                    FROM (
                        SELECT
                            id,
                            row_number() OVER(ORDER BY title) AS n
                        FROM dvd.films
                        ORDER BY title
                    )_
                    WHERE id = :id
                SQL, ['id' => $id]);
    }
    
    /**
     * При заданном фильтре возвращает число элементов в таблице dvd.films
     * 
     * @param FilmFilterDto $dto
     * @return int
     */
    public function count(FilmFilterDto $dto): int
    {
        return Film::filter($dto)->count();
    }
    
    public function getList(): FilmCollection
    {
        return $this->getListWithFilter(new FilmFilterDto('', '', '', ''));
    }
    
    /**
     * При заданном фильтре возвращает коллекцию элементов из таблицы dvd.films
     * с количественным ограничением 
     * 
     * @param FilmFilterDto $dto
     * @return FilmCollection
     */
    public function getListWithFilter(FilmFilterDto $dto): FilmCollection
    {
        return Film::with('language:id,name')
                ->select('id', 'title', 'description', 'language_id', 'release_year')
                ->filter($dto)
                ->orderBy('title')
                ->limit(AppServiceProvider::DEFAULT_LIMIT)
                ->get();
    }
    
    /**
     * Жадная загрузка фильмов с актёрами
     * 
     * @param int $id
     * @return Film
     */
    public function getByIdWithActors(int $id): Film
    {
        return Film::with([
                'language:id,name',
                'actors' => function (Builder $query) {
                    $query->select('id', 'first_name', 'last_name')
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->find($id);
    }
    
    /**
     * Извлекает по частям все данные таблицы 'dvd.films'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Film::select('id', 'title', 'description', 'release_year', 'language_id')->orderBy('id')
                ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
                ->each($callback);
    }
}

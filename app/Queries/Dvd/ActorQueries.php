<?php

namespace App\Queries\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Providers\AppServiceProvider;
use App\Queries\DBqueries;
use App\Queries\QueriesInterface;
use App\Support\Collections\Dvd\ActorCollection;

class ActorQueries extends DBqueries implements QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'dvd.actors' нет записи с id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 10;
    
    public function exists(int $id): bool
    {
        return Actor::where('id', $id)->exists();
    }
    
    public function getById(int $id): Actor
    {
        return Actor::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function count(ActorFilterDto $dto): int
    {
        return Actor::filter($dto)->count();
    }
    
    /**
     * Возвращает порядковый номер актёра (элемент таблицы 'dvd.actors')
     * с id = $id при сортировке по имени и фамилии.
     * 
     * @param int $id
     * @return int|null
     */
    public function getNumberInTableByIdWithOrderByFirstNameAndLastName(int $id): int|null
    {
        return $this->getValue(<<<SQL
                    SELECT 
                        n
                    FROM (
                        SELECT
                            id,
                            row_number() OVER(ORDER BY first_name, last_name) AS n
                        FROM dvd.actors
                        ORDER BY first_name, last_name 
                    )_
                    WHERE id = :id
                SQL, ['id' => $id]);
    }
    
    public function getList(): ActorCollection
    {
        return $this->getListWithFilter(new ActorFilterDto(''));
    }
    
    public function getListWithFilter(ActorFilterDto $dto): ActorCollection
    {
        return Actor::select(
                    'id',
                    'first_name',
                    'last_name'
                )
                ->filter($dto)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(AppServiceProvider::DEFAULT_LIMIT)
                ->get();
    }
    
    /**
     * Извлекает по частям все данные таблицы 'dvd.actors'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Actor::select('id', 'first_name', 'last_name')->orderBy('id')
                ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
                ->each($callback);
    }
}

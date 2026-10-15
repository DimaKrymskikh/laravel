<?php

namespace App\Queries\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Models\Dvd\Actor;
use App\Providers\AppServiceProvider;
use App\Queries\ModelQueries;
use App\Support\Collections\Dvd\ActorCollection;

class ActorQueries extends ModelQueries
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 10;
    
    protected function setClassName(): void
    {
        $this->className = Actor::class;
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

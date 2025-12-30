<?php

namespace App\Queries\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Queries\QueriesInterface;
use App\Support\Collections\Dvd\ActorCollection;

interface ActorQueriesInterface extends QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'dvd.actors' нет записи с id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 10;
    
    public function count(ActorFilterDto $dto): int;
    
    public function getListWithFilter(ActorFilterDto $dto): ActorCollection;
    
    public function getNumberInTableByIdWithOrderByFirstNameAndLastName(int $id): int|null;
    
    /**
     * Извлекает по частям все данные таблицы 'dvd.actors'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void;
}

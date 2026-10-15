<?php

namespace App\Contracts\Database;

use App\Contracts\Database\DatabaseQueryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * {@inheritDoc}
 * Содержит основные select-запросы.
 */
interface QueriesInterface extends DatabaseQueryInterface
{
    /**
     * Существует ли в таблице запись с первичным ключом id
     * 
     * @param string $className ORM-модель, соответствующая таблице базы данных.
     * @param int $id
     * @return bool
     */
    public function exists(string $className, int $id): bool;
    
    /**
     * Получить из таблицы запись с первичным ключом id
     * 
     * @param string $className ORM-модель, соответствующая таблице базы данных.
     * @param int $id
     * @return Model|null
     */
    public function find(string $className, int $id): Model|null;
    
    /**
     * Получить все ряды таблицы
     * 
     * @param string $className ORM-модель, соответствующая таблице базы данных.
     * @return Collection
     */
    public function all(string $className): Collection;
}

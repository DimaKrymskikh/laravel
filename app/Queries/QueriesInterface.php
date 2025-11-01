<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface QueriesInterface
{
    /**
     * Существует ли в таблице запись с первичным ключом id
     * 
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;
    
    /**
     * Получить из таблицы запись с первичным ключом id
     * 
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model;
    
    public function getList(): Collection;
}

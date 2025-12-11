<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface QueriesInterface
{
    /**
     * Существует ли в таблице запись с первичным ключом id
     * 
     * @param int $id - первичный ключ таблицы
     * @return bool
     */
    public function exists(int $id): bool;
    
    /**
     * Получить из таблицы запись с первичным ключом id
     * 
     * @param int $id - первичный ключ таблицы
     * @return Model
     */
    public function getById(int $id): Model;
    
    /**
     * Получить все ряды таблицы
     * 
     * @return Collection
     */
    public function getList(): Collection;
}

<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface SimpleQueriesInterface
{
    /**
     * Существует ли в таблице запись с данным id
     * 
     * @param int $id
     * @return bool - true, если запись существует
     */
    public function exists(int $id): bool;
    
    /**
     * Получить из таблицы запись с данным id
     * 
     * @param int $id
     * @return Model
     */
    public function getById(int $id): Model;
    
    /**
     * При заданной сортировке возвращает коллекцию, в которой id и порядковый номер n,
     * определяемый сортировкой
     * 
     * @return Collection
     */
    public function getRowNumbers(): Collection;
}

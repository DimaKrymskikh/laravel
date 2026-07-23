<?php

namespace App\Modifiers;

use App\Services\DatabaseQueryInterface;
use Illuminate\Database\Eloquent\Model;

interface ModifiersInterface extends DatabaseQueryInterface
{
    /**
     * Создаёт новую запись или изменяет запись в таблице базы данных
     * 
     * @param Model $model
     * @return void
     */
    public function save(Model $model): void;
    
    /**
     * Удаляет запись из таблицы базы данных
     * 
     * @param Model $model
     * @return void
     */
    public function remove(Model $model): void;
}

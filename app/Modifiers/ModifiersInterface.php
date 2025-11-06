<?php

namespace App\Modifiers;

use Illuminate\Database\Eloquent\Model;

interface ModifiersInterface
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

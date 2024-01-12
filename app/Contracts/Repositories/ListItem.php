<?php

namespace App\Contracts\Repositories;

use Illuminate\Http\Request;

interface ListItem
{
    /**
     * Возвращает порядковый номер элемента в списке/таблице элементов
     * 
     * @param Request $request
     * @param int $id - id элемента в таблице
     * @return int
     */
    public function getSerialNumberOfItemInList(Request $request, int $id): int;
    
    /**
     * Возвращает общее число элементов в списке/таблице элементов
     * 
     * @param Request $request
     * @return int
     */
    public function getNumberOfItemsInList(Request $request): int;
}

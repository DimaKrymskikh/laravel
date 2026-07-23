<?php

namespace App\Queries\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\Services\DatabaseQueryInterface;
use App\Support\Collections\Thesaurus\TimezoneCollection;

class TimezoneQueries implements DatabaseQueryInterface
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;

    public function getList(string $name): TimezoneCollection
    {
        return Timezone::select('id', 'name')
                ->where('name', 'ilike', "%$name%")
                ->orderBy('name')
                ->get();
    }
    
    /**
     * Извлекает по частям все данные таблицы 'thesaurus.timezones'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Timezone::select('id', 'name')->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
}

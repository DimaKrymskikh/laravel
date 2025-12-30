<?php

namespace App\Queries\Thesaurus\Timezones;

use App\Support\Collections\Thesaurus\TimezoneCollection;

interface TimezoneQueriesInterface
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;

    public function getList(string $name): TimezoneCollection;
    
    /**
     * Извлекает по частям все данные таблицы 'thesaurus.timezones'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void;
}

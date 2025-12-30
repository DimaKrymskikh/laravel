<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Queries\QueriesInterface;
use App\Support\Collections\Thesaurus\LanguageCollection;

interface LanguageQueriesInterface extends QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.languages' нет записи с id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    public function getListWithFilter(LanguageFilterDto $dto): LanguageCollection;
    
    /**
     * Извлекает по частям все данные таблицы 'thesaurus.languages'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void;
}

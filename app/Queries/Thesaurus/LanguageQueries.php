<?php

namespace App\Queries\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use App\Queries\ModelQueries;
use App\Support\Collections\Thesaurus\LanguageCollection;

class LanguageQueries extends ModelQueries
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    protected function setClassName(): void
    {
        $this->className = Language::class;
    }
    
    public function getListWithFilter(LanguageFilterDto $dto): LanguageCollection
    {
        return Language::select('id', 'name')
                    ->filter($dto)
                    ->orderBy('name')
                    ->get();
    }
    
    /**
     * Извлекает по частям все данные таблицы 'thesaurus.languages'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Language::select('id', 'name')->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
}

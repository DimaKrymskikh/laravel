<?php

namespace App\Queries\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use App\Queries\QueriesInterface;
use App\Support\Collections\Thesaurus\LanguageCollection;

class LanguageQueries implements QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.languages' нет записи с id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    public function exists(int $id): bool
    {
        return Language::where('id', $id)->exists();
    }
    
    public function getById(int $id): Language
    {
        return Language::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getList(): LanguageCollection
    {
        return $this->getListWithFilter(new LanguageFilterDto(''));
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

<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use App\Support\Collections\Thesaurus\LanguageCollection;

final class LanguageQueries implements LanguageQueriesInterface
{
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
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Language::select('id', 'name')->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
}

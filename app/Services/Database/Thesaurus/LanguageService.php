<?php

namespace App\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use App\Modifiers\Thesaurus\Languages\LanguageModifiersInterface;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

final class LanguageService
{
    public function __construct(
            private LanguageModifiersInterface $languageModifiers,
            private LanguageQueriesInterface $languageQueries,
    ) {
    }
    
    public function create(string $name): void
    {
        $this->languageModifiers->save(new Language(), $name);
    }
    
    public function update(string $name, int $id): void
    {
        $language = $this->languageQueries->getById($id);
        $this->languageModifiers->save($language, $name);
    }
    
    public function delete(int $id): void
    {
        if(!$this->languageQueries->exists($id)) {
            throw new DatabaseException("В таблице 'thesaurus.languages' нет записи с id=$id");
        }
        
        $this->languageModifiers->delete($id);
    }
    
    public function getAllLanguagesList(LanguageFilterDto $languageDto): Collection
    {
        return $this->languageQueries->getList($languageDto);
    }
}

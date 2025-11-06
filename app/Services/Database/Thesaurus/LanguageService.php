<?php

namespace App\Services\Database\Thesaurus;

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
    
    public function create(string $name): Language
    {
        $language = new Language();
        $language->name = $name;
        
        $this->languageModifiers->save($language);
        
        return $language;
    }
    
    public function update(string $name, int $id): Language
    {
        $language = $this->languageQueries->getById($id);
        $language->name = $name;
        
        $this->languageModifiers->save($language);
        
        return $language;
    }
    
    public function delete(int $id): void
    {
        $language = $this->languageQueries->getById($id);
        $this->languageModifiers->remove($language);
    }
    
    public function getAllLanguagesList(LanguageFilterDto $languageDto): Collection
    {
        return $this->languageQueries->getListWithFilter($languageDto);
    }
}

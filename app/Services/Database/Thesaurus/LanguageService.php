<?php

namespace App\Services\Database\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use App\Modifiers\Thesaurus\LanguageModifiers;
use App\Queries\Thesaurus\LanguageQueries;
use App\Services\ServiceManagerInterface;
use App\Support\Collections\Thesaurus\LanguageCollection;

final class LanguageService
{
    private LanguageModifiers $languageModifiers;
    private LanguageQueries $languageQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->languageModifiers = $this->serviceManager->getQueriesOrModifiers(LanguageModifiers::class);
        $this->languageQueries = $this->serviceManager->getQueriesOrModifiers(LanguageQueries::class);
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
    
    public function getAllLanguagesList(LanguageFilterDto $languageDto): LanguageCollection
    {
        return $this->languageQueries->getListWithFilter($languageDto);
    }
}

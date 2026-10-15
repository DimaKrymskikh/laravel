<?php

namespace App\Services\Database\Thesaurus;

use App\Contracts\Services\ServiceManagerInterface;
use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use App\Modifiers\Thesaurus\LanguageModifiers;
use App\Queries\Thesaurus\LanguageQueries;
use App\Support\Collections\Thesaurus\LanguageCollection;

final class LanguageService
{
    public const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.languages' нет записи с id=%d";
    
    private LanguageModifiers $languageModifiers;
    private LanguageQueries $languageQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->languageModifiers = $this->serviceManager->getQueriesOrModifiers(LanguageModifiers::class);
        $this->languageQueries = $this->serviceManager->getQueriesOrModifiers(LanguageQueries::class);
    }
    
    public function getLanguageById(int $id): Language
    {
        return $this->languageQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
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
        $language = $this->getLanguageById($id);
        $language->name = $name;
        
        $this->languageModifiers->save($language);
        
        return $language;
    }
    
    public function delete(int $id): void
    {
        $language = $this->getLanguageById($id);
        $this->languageModifiers->remove($language);
    }
    
    public function getAllLanguagesList(LanguageFilterDto $languageDto): LanguageCollection
    {
        return $this->languageQueries->getListWithFilter($languageDto);
    }
}

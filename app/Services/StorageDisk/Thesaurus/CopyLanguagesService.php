<?php

namespace App\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Language;
use App\Queries\Thesaurus\LanguageQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyist;

final class CopyLanguagesService
{
    private LanguageQueries $queries;
    private LanguagesCopyist $copyist;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(LanguageQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(LanguagesCopyist::class);
    }
    
    /**
     * Извлекает данные из таблицы 'thesaurus.languages' и создаёт класс \Database\Copy\Thesaurus\LanguageData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'Thesaurus/LanguageData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\Thesaurus', 'thesaurus.languages', 'LanguageData');
        
        $this->queries->getListInLazyById(fn (Language $language) => $this->copyist->writeData($file, $language));
       
        $this->copyist->writeFooter($file);
    }
}

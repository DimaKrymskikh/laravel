<?php

namespace App\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Language;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\LanguagesCopyistInterface;

final class CopyLanguagesService
{
    public function __construct(
            private LanguageQueriesInterface $queries,
            private LanguagesCopyistInterface $copyist,
    ) {
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

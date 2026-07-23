<?php

namespace App\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyist;

final class CopyTimezonesService
{
    private TimezoneQueries $queries;
    private TimezonesCopyist $copyist;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(TimezoneQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(TimezonesCopyist::class);
    }
    
    /**
     * Извлекает данные из таблицы 'thesaurus.timezones' и создаёт класс \Database\Copy\Thesaurus\TimezoneData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'Thesaurus/TimezoneData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\Thesaurus', 'thesaurus.timezones', 'TimezoneData');
        
        $this->queries->getListInLazyById(fn (Timezone $tz) => $this->copyist->writeData($file, $tz));
       
        $this->copyist->writeFooter($file);
    }
}

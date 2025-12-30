<?php

namespace App\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\TimezonesCopyistInterface;

final class CopyTimezonesService
{
    public function __construct(
            private TimezoneQueriesInterface $queries,
            private TimezonesCopyistInterface $copyist,
    ) {
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

<?php

namespace App\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyistInterface;

final class CopyCitiesService
{
    public function __construct(
            private CityQueriesInterface $queries,
            private CitiesCopyistInterface $copyist
    ) {
    }
    
    /**
     * Извлекает данные из таблицы 'thesaurus.cities' и создаёт класс \Database\Copy\Thesaurus\CityData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'Thesaurus/CityData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\Thesaurus', 'thesaurus.cities', 'CityData');
        
        $this->queries->getListInLazyById(fn (City $city) => $this->copyist->writeData($file, $city));
       
        $this->copyist->writeFooter($file);
    }
}

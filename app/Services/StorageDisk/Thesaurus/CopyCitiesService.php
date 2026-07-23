<?php

namespace App\Services\StorageDisk\Thesaurus;

use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\CityQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus\CitiesCopyist;

final class CopyCitiesService
{
    private CityQueries $queries;
    private CitiesCopyist $copyist;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(CityQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(CitiesCopyist::class);
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

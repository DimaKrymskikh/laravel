<?php

namespace App\Services\StorageDisk\Dvd;

use App\Models\Dvd\Actor;
use App\Queries\Dvd\ActorQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyist;

final class CopyActorsService
{
    private ActorQueries $queries;
    private ActorsCopyist $copyist;
            
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(ActorQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(ActorsCopyist::class);
    }
    
    /**
     * Извлекает данные из таблицы 'dvd.actors' и создаёт класс \Database\Copy\Dvd\ActorData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'Dvd/ActorData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\Dvd', 'dvd.actors', 'ActorData');
        
        $this->queries->getListInLazyById(fn (Actor $actor) => $this->copyist->writeData($file, $actor));
       
        $this->copyist->writeFooter($file);
    }
}

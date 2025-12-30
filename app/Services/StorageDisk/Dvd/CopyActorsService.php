<?php

namespace App\Services\StorageDisk\Dvd;

use App\Models\Dvd\Actor;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\ActorsCopyistInterface;

final class CopyActorsService
{
    public function __construct(
            private ActorQueriesInterface $queries,
            private ActorsCopyistInterface $copyist
    ) {
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

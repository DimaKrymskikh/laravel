<?php

namespace App\Services\StorageDisk\Dvd;

use App\Models\Dvd\FilmActor;
use App\Queries\Dvd\FilmActorQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyist;

final class CopyFilmsActorsService
{
    private FilmActorQueries $queries;
    private FilmsActorsCopyist $copyist;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(FilmActorQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(FilmsActorsCopyist::class);
    }
    
    /**
     * Извлекает данные из таблицы 'dvd.films_actors' и создаёт класс \Database\Copy\Dvd\FilmActorData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'Dvd/FilmActorData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\Dvd', 'dvd.films_actors', 'FilmActorData');
        
        $this->queries->getListInLazy(fn (FilmActor $filmActor) => $this->copyist->writeData($file, $filmActor));
        
        $this->copyist->writeFooter($file);
    }
}

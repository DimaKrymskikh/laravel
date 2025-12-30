<?php

namespace App\Services\StorageDisk\Dvd;

use App\Models\Dvd\FilmActor;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsActorsCopyistInterface;

final class CopyFilmsActorsService
{
    public function __construct(
            private FilmActorQueriesInterface $queries,
            private FilmsActorsCopyistInterface $copyist,
    ) {
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

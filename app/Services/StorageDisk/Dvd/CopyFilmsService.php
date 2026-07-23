<?php

namespace App\Services\StorageDisk\Dvd;

use App\Models\Dvd\Film;
use App\Queries\Dvd\FilmQueries;
use App\Services\ServiceManagerInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyist;

final class CopyFilmsService
{
    private FilmQueries $queries;
    private FilmsCopyist $copyist;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->queries = $this->serviceManager->getQueriesOrModifiers(FilmQueries::class);
        $this->copyist = $this->serviceManager->getCopyist(FilmsCopyist::class);
    }
    
    /**
     * Извлекает данные из таблицы 'dvd.films' и создаёт класс \Database\Copy\Dvd\FilmData, хранящий эти данные.
     * 
     * @return void
     */
    public function copy(): void
    {
        $file = 'Dvd/FilmData.php';
        
        $this->copyist->writeHeader($file, 'Database\Copy\Dvd', 'dvd.films', 'FilmData');
        
        $this->queries->getListInLazyById(fn (Film $film) => $this->copyist->writeData($file, $film));
        
        $this->copyist->writeFooter($file);
    }
}

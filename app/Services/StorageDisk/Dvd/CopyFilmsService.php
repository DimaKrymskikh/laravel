<?php

namespace App\Services\StorageDisk\Dvd;

use App\Models\Dvd\Film;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\StorageDisk\CopyingDatabaseDataToFile\Dvd\FilmsCopyistInterface;

final class CopyFilmsService
{
    public function __construct(
            private FilmQueriesInterface $queries,
            private FilmsCopyistInterface $copyist
    ) {
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

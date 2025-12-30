<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Dvd;

use App\Models\Dvd\Film;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface FilmsCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели Film
     * 
     * @param string $file
     * @param Film $film
     * @return void
     */
    public function writeData(string $file, Film $film): void;
}

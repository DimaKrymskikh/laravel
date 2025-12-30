<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Dvd;

use App\Models\Dvd\FilmActor;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface FilmsActorsCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели FilmActor
     * 
     * @param string $file
     * @param FilmActor $filmActor
     * @return void
     */
    public function writeData(string $file, FilmActor $filmActor): void;
}

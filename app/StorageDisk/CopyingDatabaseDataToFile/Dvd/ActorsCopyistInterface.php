<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Dvd;

use App\Models\Dvd\Actor;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface ActorsCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели Actor
     *
     * @param string $file
     * @param Actor $actor
     * @return void
     */
    public function writeData(string $file, Actor $actor): void;
}

<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\City;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface CitiesCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели City
     * 
     * @param string $file
     * @param City $city
     * @return void
     */
    public function writeData(string $file, City $city): void;
}

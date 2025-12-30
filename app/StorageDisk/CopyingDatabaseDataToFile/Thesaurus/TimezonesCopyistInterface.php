<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface TimezonesCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели Timezone
     * 
     * @param string $file
     * @param Timezone $timezone
     * @return void
     */
    public function writeData(string $file, Timezone $timezone): void;
}

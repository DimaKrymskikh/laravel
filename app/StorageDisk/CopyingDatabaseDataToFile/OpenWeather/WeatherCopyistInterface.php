<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface WeatherCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели Weather
     * 
     * @param string $file
     * @param Weather $weather
     * @return void
     */
    public function writeData(string $file, Weather $weather): void;
}

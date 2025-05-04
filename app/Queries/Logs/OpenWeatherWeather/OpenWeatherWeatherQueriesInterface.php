<?php

namespace App\Queries\Logs\OpenWeatherWeather;

interface OpenWeatherWeatherQueriesInterface
{
    public function getNumberOfWeatherLinesForLastMinute(): int;
    
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool;
}

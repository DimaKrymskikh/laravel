<?php

namespace App\Queries\Logs\OpenWeatherWeather;

use App\Queries\DBqueriesInterface;

interface OpenWeatherWeatherQueriesInterface extends DBqueriesInterface
{
    /**
     * Возвращает число строк с данными о погоде за последнюю минуту.
     * 
     * @return int
     */
    public function getNumberOfWeatherLinesForLastMinute(): int;
    
    /**
     * Определяет, имеются ли данные о погоде для города $city за последнии 
     * GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * 
     * @param int $cityId
     * @return bool
     */
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool;
}

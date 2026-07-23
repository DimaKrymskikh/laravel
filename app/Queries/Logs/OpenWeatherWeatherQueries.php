<?php

namespace App\Queries\Logs;

use App\Queries\DBqueries;
use App\Services\OpenWeather\WeatherService;
use App\Models\Logs\OpenWeatherWeather;

class OpenWeatherWeatherQueries extends DBqueries
{
    /**
     * Возвращает число строк с данными о погоде за последнюю минуту.
     * 
     * @return int
     */
    public function getNumberOfWeatherLinesForLastMinute(): int
    {
        return OpenWeatherWeather::whereRaw("created_at > now() - interval '1 minute'")->count();
    }
    
    /**
     * Определяет, имеются ли данные о погоде для города $city за последнии 
     * WeatherService::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * 
     * @param int $cityId
     * @return bool
     */
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool
    {
        $period = WeatherService::OPEN_WEATHER_CITY_UPDATE_PERIOD;
        
        return OpenWeatherWeather::where('city_id', $cityId)
                ->whereRaw("created_at > now() - interval '$period minute'")
                ->exists();
    }
}

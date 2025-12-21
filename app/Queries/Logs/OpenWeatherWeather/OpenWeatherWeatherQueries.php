<?php

namespace App\Queries\Logs\OpenWeatherWeather;

use App\Queries\DBqueries;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use App\Models\Logs\OpenWeatherWeather;

final class OpenWeatherWeatherQueries extends DBqueries implements OpenWeatherWeatherQueriesInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getNumberOfWeatherLinesForLastMinute(): int
    {
        return OpenWeatherWeather::whereRaw("created_at > now() - interval '1 minute'")->count();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool
    {
        $period = OpenWeatherWeatherService::OPEN_WEATHER_CITY_UPDATE_PERIOD;
        
        return OpenWeatherWeather::where('city_id', $cityId)
                ->whereRaw("created_at > now() - interval '$period minute'")
                ->exists();
    }
}

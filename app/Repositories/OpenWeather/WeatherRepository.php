<?php

namespace App\Repositories\OpenWeather;

use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Carbon\Carbon;

class WeatherRepository
{
    /**
     * Возвращает последние данные о погоде для города $city
     * 
     * @param City $city
     * @return Weather
     */
    public function getLatestWeatherForOneCity(City $city): Weather
    {
        $weather = Weather::select(
                        'city_id',
                        'weather_description',
                        'main_temp',
                        'main_feels_like',
                        'main_pressure',
                        'main_humidity',
                        'visibility',
                        'wind_speed',
                        'wind_deg',
                        'clouds_all',
                        'created_at'
                    )
                ->where('city_id', $city->id)
                ->orderBy('created_at', 'desc')
                ->first();
        
        // Время получения данных о погоде преобразуем в фактическое время в городе $city
        $tzName = $city->timezone_id ? $city->timezone->name : TimezoneInterface::DEFAULT_TIMEZONE_NAME;
        $weather->created_at = Carbon::parse($weather->created_at)->setTimezone($tzName);
        
        return $weather;
    }
}

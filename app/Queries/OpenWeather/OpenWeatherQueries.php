<?php

namespace App\Queries\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\Services\DatabaseQueryInterface;

class OpenWeatherQueries implements DatabaseQueryInterface
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    /**
     * Извлекает по частям все данные таблицы 'open_weather.weather'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void
    {
        Weather::select(
                'id',
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
            )->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
    
    /**
     * Извлекает погоду по id-города из таблицы 'open_weather.weather'.
     * 
     * @param int $cityId
     * @return Weather
     */
    public function getByCityId(int $cityId): Weather
    {
        return Weather::where('city_id', $cityId)->first();
    }
}

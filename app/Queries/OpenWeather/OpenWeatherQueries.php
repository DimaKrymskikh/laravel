<?php

namespace App\Queries\OpenWeather;

use App\Models\OpenWeather\Weather;

final class OpenWeatherQueries implements OpenWeatherQueriesInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
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
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getByCityId(int $cityId): Weather
    {
        return Weather::where('city_id', $cityId)->first();
    }
}

<?php

namespace App\Queries\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Models\Logs\OpenWeatherWeather;
use Illuminate\Database\Eloquent\Builder;

final class OpenWeatherWeatherQueries
{
    public function queryWeatherList(WeatherFilterDto $dto, int $cityId): Builder
    {
        return OpenWeatherWeather::select(
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
                    ->where('city_id', $cityId)
                    ->filter($dto)
                    ->orderBy('created_at', 'desc');
    }
}
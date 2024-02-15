<?php

namespace App\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;

final class WeatherService
{
    public function create(WeatherDto $dto): Weather
    {
        $weather = new Weather();
        $weather->city_id = $dto->cityId;
        $weather->weather_description = $dto->weatherDescription;
        $weather->main_temp = $dto->mainTemp;
        $weather->main_feels_like = $dto->mainFeelsLike;
        $weather->main_pressure = $dto->mainPressure;
        $weather->main_humidity = $dto->mainHumidity;
        $weather->visibility = $dto->visibility;
        $weather->wind_speed = $dto->windSpeed;
        $weather->wind_deg = $dto->windDeg;
        $weather->clouds_all = $dto->cloudsAll;
        
        $weather->save();
        
        return $weather;
    }
}

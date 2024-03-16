<?php

namespace App\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;

final class WeatherService
{
    public function create(WeatherDto $dto): Weather
    {
        $data = $dto->openWeatherObject->data;
        
        $weather = new Weather();
        $weather->city_id = $dto->cityId;
        $weather->weather_description = $data->weatherDescription;
        $weather->main_temp = $data->mainTemp;
        $weather->main_feels_like = $data->mainFeelsLike;
        $weather->main_pressure = $data->mainPressure;
        $weather->main_humidity = $data->mainHumidity;
        $weather->visibility = $data->visibility;
        $weather->wind_speed = $data->windSpeed;
        $weather->wind_deg = $data->windDeg;
        $weather->clouds_all = $data->cloudsAll;
        
        $weather->save();
        
        return $weather;
    }
}

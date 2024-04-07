<?php

namespace App\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use Carbon\Carbon;

final class WeatherService
{
    public function updateOrCreate(WeatherDto $dto): Weather
    {
        $data = $dto->openWeatherObject->data;
         
        return Weather::updateOrCreate([
                'city_id' => $dto->cityId,
            ], [
                'weather_description' => $data->weatherDescription,
                'main_temp' => $data->mainTemp,
                'main_feels_like' => $data->mainFeelsLike,
                'main_pressure' => $data->mainPressure,
                'main_humidity' => $data->mainHumidity,
                'visibility' => $data->visibility,
                'wind_speed' => $data->windSpeed,
                'wind_deg' => $data->windDeg,
                'clouds_all' => $data->cloudsAll,
                // Время нужно задавать с часовым поясом 'UTC'
                'created_at' => Carbon::now('UTC'),
            ]);
    }
}

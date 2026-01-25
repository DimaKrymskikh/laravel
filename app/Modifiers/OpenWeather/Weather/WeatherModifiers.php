<?php

namespace App\Modifiers\OpenWeather\Weather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Modifiers\Modifiers;
use Carbon\Carbon;

final class WeatherModifiers extends Modifiers implements WeatherModifiersInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function updateOrCreate(WeatherDto $dto): void
    {
        $data = $dto->openWeatherObject->data;
         
        Weather::updateOrCreate([
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

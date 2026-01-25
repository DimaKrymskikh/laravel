<?php

namespace App\DataTransferObjects\Database\OpenWeather;

use App\ValueObjects\ResponseObjects\OpenWeatherObject;

/**
 * Класс хранит данные для записи в таблицу 'open_weather.weather'.
 */
readonly final class WeatherDto
{
    public function __construct(
            public int $cityId,
            public OpenWeatherObject $openWeatherObject,
    )
    {}
}

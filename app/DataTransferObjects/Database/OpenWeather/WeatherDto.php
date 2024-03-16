<?php

namespace App\DataTransferObjects\Database\OpenWeather;

use App\ValueObjects\ResponseObjects\OpenWeatherObject;

readonly final class WeatherDto
{
    public function __construct(
            public int $cityId,
            public OpenWeatherObject $openWeatherObject,
    )
    {}
}

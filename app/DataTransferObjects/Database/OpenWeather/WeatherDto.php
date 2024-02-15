<?php

namespace App\DataTransferObjects\Database\OpenWeather;

readonly final class WeatherDto
{
    public function __construct(
            public int $cityId,
            public string $weatherDescription,
            public float $mainTemp,
            public float $mainFeelsLike,
            public int $mainPressure,
            public int $mainHumidity,
            public int $visibility,
            public float $windSpeed,
            public int $windDeg,
            public int $cloudsAll,
    )
    {}
}

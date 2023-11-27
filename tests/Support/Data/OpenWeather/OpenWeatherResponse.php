<?php

namespace Tests\Support\Data\OpenWeather;

trait OpenWeatherResponse
{
    private function getWeatherForOneCity(): array
    {
        return [
            'weather' => [
                (object) [
                    'description' => 'Хорошая погода'
                ]
            ],
            'main' => (object) [
                'temp' => 11.7,
                'feels_like' => 12,
                'pressure' => 1000,
                'humidity' => 77,
            ],
            'visibility' => 500,
            'wind' => (object) [
                'speed' => 2.5,
                'deg' => 120
            ],
            'clouds' => (object) [
                'all' => 100
            ]
        ];
    }
}

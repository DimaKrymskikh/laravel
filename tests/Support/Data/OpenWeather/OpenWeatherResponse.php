<?php

namespace Tests\Support\Data\OpenWeather;

trait OpenWeatherResponse
{
    private function getWeatherForOneCity(): array
    {
        // Отсутствует $arr['main']->feels_like
        return [
            'weather' => [
                (object) [
                    'description' => 'Хорошая погода'
                ]
            ],
            'main' => (object) [
                'temp' => 11.7,
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

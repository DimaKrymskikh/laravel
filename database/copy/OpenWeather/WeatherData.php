<?php

namespace Database\Copy\OpenWeather;

// Данные таблицы open_weather.weather
class WeatherData
{
    public function __invoke(): array
    {
        return [
            (object) [
                'id' => 1,
                'city_id' => 2,
                'weather_description' => 'ясно',
                'main_temp' => 9.61,
                'main_feels_like' => 6.75,
                'main_pressure' => 1022,
                'main_humidity' => 62,
                'visibility' => 10000,
                'wind_speed' => 6,
                'wind_deg' => 210,
                'clouds_all' => 0,
                'created_at' => '2024-04-04 10:05:51',
            ],
            (object) [
                'id' => 2,
                'city_id' => 4,
                'weather_description' => 'пасмурно',
                'main_temp' => 5.8,
                'main_feels_like' => 3.01,
                'main_pressure' => 1008,
                'main_humidity' => 86,
                'visibility' => 1631,
                'wind_speed' => 3.71,
                'wind_deg' => 285,
                'clouds_all' => 100,
                'created_at' => '2024-04-04 10:05:52',
            ],
            (object) [
                'id' => 3,
                'city_id' => 7,
                'weather_description' => 'ясно',
                'main_temp' => 6.41,
                'main_feels_like' => 2.01,
                'main_pressure' => 1017,
                'main_humidity' => 70,
                'visibility' => 10000,
                'wind_speed' => 8,
                'wind_deg' => 210,
                'clouds_all' => 0,
                'created_at' => '2024-04-04 10:05:52',
            ],
            (object) [
                'id' => 4,
                'city_id' => 6,
                'weather_description' => 'ясно',
                'main_temp' => 7.94,
                'main_feels_like' => 5.47,
                'main_pressure' => 1023,
                'main_humidity' => 71,
                'visibility' => 10000,
                'wind_speed' => 4,
                'wind_deg' => 220,
                'clouds_all' => 0,
                'created_at' => '2024-04-04 10:05:52',
            ],
            (object) [
                'id' => 5,
                'city_id' => 9,
                'weather_description' => 'пасмурно',
                'main_temp' => 10.01,
                'main_feels_like' => 8.69,
                'main_pressure' => 1018,
                'main_humidity' => 62,
                'visibility' => 10000,
                'wind_speed' => 2.59,
                'wind_deg' => 234,
                'clouds_all' => 90,
                'created_at' => '2024-04-04 10:05:52',
            ],
            (object) [
                'id' => 6,
                'city_id' => 8,
                'weather_description' => 'ясно',
                'main_temp' => 10.33,
                'main_feels_like' => 8.65,
                'main_pressure' => 1026,
                'main_humidity' => 47,
                'visibility' => 10000,
                'wind_speed' => 7,
                'wind_deg' => 230,
                'clouds_all' => 0,
                'created_at' => '2024-04-04 10:05:53',
            ],
        ];
    }
}

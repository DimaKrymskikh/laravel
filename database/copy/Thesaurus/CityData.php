<?php

namespace Database\Copy\Thesaurus;

// Данные таблицы thesaurus.cities
class CityData
{
    public function __invoke(): array
    {
        return [
            (object) [
                'id' => 2,
                'name' => 'Новосибирск',
                'open_weather_id' => 1496747,
                'timezone_id' => 259,
            ],
            (object) [
                'id' => 4,
                'name' => 'Москва',
                'open_weather_id' => 524901,
                'timezone_id' => 344,
            ],
            (object) [
                'id' => 6,
                'name' => 'Омск',
                'open_weather_id' => 1496153,
                'timezone_id' => 260,
            ],
            (object) [
                'id' => 7,
                'name' => 'Томск',
                'open_weather_id' => 1489425,
                'timezone_id' => 281,
            ],
            (object) [
                'id' => 8,
                'name' => 'Барнаул',
                'open_weather_id' => 1510853,
                'timezone_id' => null,
            ],
            (object) [
                'id' => 9,
                'name' => 'Владивосток',
                'open_weather_id' => 2013348,
                'timezone_id' => 286,
            ],
        ];
    }
}

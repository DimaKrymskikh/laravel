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
            ],
            (object) [
                'id' => 4,
                'name' => 'Москва',
                'open_weather_id' => 524901,
            ],
            (object) [
                'id' => 6,
                'name' => 'Омск',
                'open_weather_id' => 1496153,
            ],
            (object) [
                'id' => 7,
                'name' => 'Томск',
                'open_weather_id' => 1489425,
            ],
        ];
    }
}

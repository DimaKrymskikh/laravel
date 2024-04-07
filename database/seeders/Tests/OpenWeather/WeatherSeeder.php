<?php

namespace Database\Seeders\Tests\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Services\Database\OpenWeather\WeatherService;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeatherSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(WeatherService $weatherService): void
    {
        foreach ($this->getWeather() as $weather) {
            $dto = new WeatherDto($weather->city_id, OpenWeatherObject::create($weather->data));
            $weatherService->updateOrCreate($dto);
        }
    }
    
    /**
     * Данные погоды заданы в формате сервиса OpenWeather
     * 
     * @return array
     */
    private function getWeather(): array
    {
        return [
            // Присутствуют все NULL-поля
            (object) [
                'city_id' => CitySeeder::ID_NOVOSIBIRSK,
                'data' => (object) [
                    'weather' => [
                        (object) [
                            'description' => 'Ясно',
                        ]
                    ],
                    'main' => (object) [
                        'temp' => 22.91,
                        'feels_like' => 20,
                        'pressure' => 1020,
                        'humidity' => 85,
                    ],
                    'visibility' => 8000,
                    'wind' => (object) [
                        'speed' => 5,25,
                        'deg' => 210,
                    ],
                    'clouds' => (object) [
                        'all' => 0,
                    ]
                ],
            ],
            (object) [
                'city_id' => CitySeeder::ID_MOSCOW,
                'data' => (object) [
                    'main' => (object) [
                        'temp' => -3,
                    ]
                ]
            ],
            (object) [
                'city_id' => CitySeeder::ID_BARNAUL,
                'data' => (object) [
                    'main' => (object) [
                        'temp' => 2,
                    ]
                ]
            ],
            // Отсутствуют все NULL-поля
            (object) [
                'city_id' => CitySeeder::ID_OMSK,
                'data' => (object) []
            ],
        ];
    }
}

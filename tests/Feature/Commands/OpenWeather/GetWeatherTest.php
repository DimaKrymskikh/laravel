<?php

namespace Tests\Feature\Commands\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GetWeatherTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_weather_can_be_get_for_one_city(): void
    {
        Http::preventStrayRequests();
        
        $city = City::factory()->create();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getResponseInstance(), 200),
        ]);

        // Команда успешно выполняется
        $this
            ->artisan("get:weather $city->open_weather_id")
            ->assertExitCode(0);
        
        // Проверяем сохранение погоды в базе
        $weather = Weather::where('city_id', $city->id)->first();
        $this->assertEquals('Хорошая погода', $weather->weather_description);
        $this->assertEquals(11.7, $weather->main_temp);
        $this->assertEquals(12, $weather->main_feels_like);
        $this->assertEquals(1000, $weather->main_pressure);
        $this->assertEquals(77, $weather->main_humidity);
        $this->assertEquals(500, $weather->visibility);
        $this->assertEquals(2.5, $weather->wind_speed);
        $this->assertEquals(120, $weather->wind_deg);
        $this->assertEquals(100, $weather->clouds_all);
    }
    
    public function test_weather_can_be_get_for_two_cities(): void
    {
        Http::preventStrayRequests();
        
        City::factory()->count(2)
                ->state(new Sequence(
                    [],
                    [
                        'id' => 2,
                        'name' => 'NewCity',
                        'open_weather_id' => 2
                    ],
                ))
                ->create();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getResponseInstance(), 200),
        ]);

        // Команда успешно выполняется
        $this
            ->artisan("get:weather")
            ->assertExitCode(0);
        
        // Проверяем сохранение погоды в базе
        $this->assertEquals(2, Weather::all()->count());
    }
    
    public function test_OpenWeather_send_429(): void
    {
        Http::preventStrayRequests();
        
        // Создаём один город, чтобы в команде был массив для перебора в foreach
        City::factory()->create();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response('Превышен лимит', 429),
        ]);

        // Команда успешно выполняется
        $this
            ->artisan("get:weather")
            ->expectsOutput("Сервер OpenWeather вернул ответ со статусом 429: Превышен лимит")
            ->assertExitCode(0);
    }
    
    private function getResponseInstance(): array
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

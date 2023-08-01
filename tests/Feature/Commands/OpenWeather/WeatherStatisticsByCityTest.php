<?php

namespace Tests\Feature\Commands\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherStatisticsByCityTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_statistics_can_be_received_for_one_city_without_weather_data(): void
    {
        $city = City::factory()->create();

        $this
            ->artisan("statistics:weather $city->open_weather_id")
            ->expectsOutput("Статистика по городу $city->name [$city->open_weather_id]")
            ->expectsOutput("Максимальная температура: отсутствует")
            ->expectsOutput("Минимальная температура: отсутствует")
            ->expectsOutput("Всего записей: 0")
            ->assertExitCode(0);
    }
    
    public function test_statistics_can_be_received_for_one_city_with_weather_data(): void
    {
        $city = City::factory()->create();
        
        Weather::factory()
                ->count(3)
                ->state(new Sequence(
                        [
                            'city_id' => $city->id,
                            'main_temp' => 10.5
                        ],
                        [
                            'city_id' => $city->id,
                            'main_temp' => -5.11
                        ],
                        [
                            'city_id' => $city->id,
                            'main_temp' => 22.14
                        ],
                    ))
                ->create();

        $this
            ->artisan("statistics:weather $city->open_weather_id")
            ->expectsOutput("Статистика по городу $city->name [$city->open_weather_id]")
            ->expectsOutput("Максимальная температура: 22.14")
            ->expectsOutput("Минимальная температура: -5.11")
            ->expectsOutput("Всего записей: 3")
            ->assertExitCode(0);
    }
    
    public function test_statistics_can_be_received_for_all_cities(): void
    {
        $city1 = City::factory()
                ->has(Weather::factory()->count(3)->state(function (array $attributes, City $city) {
                        return ['city_id' => $city->id];
                    }))
                ->create();
        
        $city2 = City::factory()
                ->has(Weather::factory()->state(function (array $attributes, City $city) {
                        return ['city_id' => $city->id];
                    }))
                ->create([
                    'name' => 'TwoCity',
                    'open_weather_id' => 2
                ]);

        $this
            ->artisan("statistics:weather")
            ->expectsOutput("Статистика погоды по городам")
            ->expectsOutput("$city1->name [$city1->open_weather_id] содержит 3 записей")
            ->expectsOutput("$city2->name [$city2->open_weather_id] содержит 1 записей")
            ->assertExitCode(0);
    }
}

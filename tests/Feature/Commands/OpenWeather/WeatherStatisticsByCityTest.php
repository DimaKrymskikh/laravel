<?php

namespace Tests\Feature\Commands\OpenWeather;

use App\Models\Thesaurus\City;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Seeders;
use Tests\TestCase;

class WeatherStatisticsByCityTest extends TestCase
{
    use RefreshDatabase, Seeders;
    
    public function test_statistics_can_be_received_for_one_city_without_weather_data(): void
    {
        $this->seedCitiesAndLogsWeather();
        // Берём город, для которого нет погоды
        $city = City::where('id', CitySeeder::ID_OMSK)->first();

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
        $this->seedCitiesAndLogsWeather();
        // Берём город, для которого была получена погода
        $city = City::where('id', CitySeeder::ID_NOVOSIBIRSK)->first();

        $this
            ->artisan("statistics:weather $city->open_weather_id")
            ->expectsOutput("Статистика по городу $city->name [$city->open_weather_id]")
            ->expectsOutput("Максимальная температура: 22.91")
            ->expectsOutput("Минимальная температура: 0.5")
            ->expectsOutput("Всего записей: 3")
            ->assertExitCode(0);
    }
    
    public function test_statistics_can_be_received_for_all_cities(): void
    {
        $this->seedCitiesAndLogsWeather();
        // Должна быть сортировка по названию города
        $cities = City::orderBy('name')->get();

        $this
            ->artisan("statistics:weather")
            ->expectsOutput("Статистика погоды по городам")
            // Барнаул
            ->expectsOutput("{$cities[0]->name} [{$cities[0]->open_weather_id}] содержит 1 записей")
            // Москва
            ->expectsOutput("{$cities[1]->name} [{$cities[1]->open_weather_id}] содержит 2 записей")
            // Новосибирск
            ->expectsOutput("{$cities[2]->name} [{$cities[2]->open_weather_id}] содержит 3 записей")
            // Омск
            ->expectsOutput("{$cities[3]->name} [{$cities[3]->open_weather_id}] содержит 0 записей")
            // Томск
            ->expectsOutput("{$cities[4]->name} [{$cities[4]->open_weather_id}] содержит 0 записей")
            ->assertExitCode(0);
    }
    
    public function test_statistics_can_not_get_with_invalid_integer(): void
    {
        // Выполняем посев городов
        $this->seedCities();

        // Запускаем команду с параметром 13
        $openWeatherId = 13;
        $this
            ->artisan("statistics:weather $openWeatherId")
            ->expectsOutput("В таблице 'thesaurus.cities' нет городов с полем open_weather_id = $openWeatherId")
            ->assertExitCode(0);
    }
    
    public function test_statistics_can_not_get_if_parameter_be_not_integer(): void
    {
        // Запускаем команду с параметром x
        $this
            ->artisan("statistics:weather x")
            ->expectsOutput(trans('commands.parameter.int'))
            ->assertExitCode(0);
    }
}

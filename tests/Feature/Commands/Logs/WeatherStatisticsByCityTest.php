<?php

namespace Tests\Feature\Commands\logs;

use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Database\Testsupport\Thesaurus\ThesaurusData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherStatisticsByCityTest extends TestCase
{
    use RefreshDatabase, ThesaurusData;
    
    public function test_statistics_can_be_received_for_one_city_without_weather_data(): void
    {
        $this->seedCitiesAndLogsWeather();
        // Берём город, для которого нет погоды
        $city = City::where('id', CitySeeder::ID_OMSK)->first();

        $this
            ->artisan("statistics:weather $city->open_weather_id")
            ->expectsOutput("Статистика по городу $city->name [$city->open_weather_id]")
            ->expectsOutput("Минимальная температура: отсутствует")
            ->expectsOutput("Максимальная температура: отсутствует")
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
            ->expectsOutput("Минимальная температура: 0.5")
            ->expectsOutput("Максимальная температура: 22.91")
            ->expectsOutput("Всего записей: 3")
            ->assertExitCode(0);
    }
    
    public function test_statistics_can_be_received_for_all_cities(): void
    {
        $this->seedCitiesAndLogsWeather();

        $this
            ->artisan("statistics:weather")
            ->expectsOutput("Статистика по всем городам.")
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
            ->expectsOutput(sprintf(CityQueriesInterface::NOT_RECORD_WITH_OPEN_WEATHER_ID, $openWeatherId))
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

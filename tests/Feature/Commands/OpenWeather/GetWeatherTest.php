<?php

namespace Tests\Feature\Commands\OpenWeather;

use App\Events\RefreshCityWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\Support\Data\OpenWeather\OpenWeatherResponse;
use Tests\Support\Seeders;
use Tests\TestCase;

class GetWeatherTest extends TestCase
{
    use RefreshDatabase, OpenWeatherResponse, Seeders;
    
    public function test_weather_can_be_get_for_one_city(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);
        
        Event::fake();
        
        // Выполняем посев городов
        $this->seedCities();
        // Берём один город, чтобы получить параметр команды
        $city = City::where('id', CitySeeder::ID_MOSCOW)->first();

        // Команда с параметром успешно выполняется
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
        
        Event::assertNotDispatched(RefreshCityWeather::class);
    }
    
    public function test_weather_can_not_get_with_invalid_integer(): void
    {
        Http::preventStrayRequests();
        
        // Выполняем посев городов
        $this->seedCities();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);

        // Запускаем команду с параметром 13
        $this
            ->artisan("get:weather 13")
            ->expectsOutput("В таблице 'thesaurus.cities' нет городов с полем open_weather_id = 13.")
            ->expectsOutput("Выполнение команды прервано.")
            ->assertExitCode(0);
        // Нет данных погоды
        $this
            ->assertEquals(0, Weather::all()->count());
    }
    
    public function test_weather_can_not_get_if_parameter_be_not_integer(): void
    {
        Http::preventStrayRequests();
        
        // Выполняем посев городов
        $this->seedCities();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);

        // Запускаем команду с параметром a
        $this
            ->artisan("get:weather a")
            ->expectsOutput("Параметр команды не является целым числом.")
            ->expectsOutput("Выполнение команды прервано.")
            ->assertExitCode(0);
        // Нет данных погоды
        $this
            ->assertEquals(0, Weather::all()->count());
    }
    
    public function test_weather_can_be_get_for_some_cities(): void
    {
        Http::preventStrayRequests();
        
        // Выполняем посев городов
        $this->seedCities();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);

        // Команда без параметра успешно выполняется
        $this
            ->artisan("get:weather")
            ->assertExitCode(0);
        
        // Проверяем сохранение погоды в базе
        // Для каждого города получена погода
        $this->assertEquals(City::all()->count(), Weather::all()->count());
    }
    
    public function test_OpenWeather_send_429(): void
    {
        Http::preventStrayRequests();
        
        // Выполняем посев городов
        $this->seedCities();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response('Превышен лимит', 429),
        ]);

        // Команда успешно выполняется
        $this
            ->artisan("get:weather")
            ->expectsOutput("Сервер OpenWeather вернул ответ со статусом 429: Превышен лимит")
            ->assertExitCode(0);
    }
    
    public function test_RefreshCityWeather_event_is_running_if_command_be_called_in_http(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);
        
        Event::fake();
        
        // Выполняем посев городов
        $this->seedCities();
        // Берём один город, чтобы получить параметр команды
        $city = City::where('id', CitySeeder::ID_NOVOSIBIRSK)->first();

        // Команда с параметром успешно выполняется
        $this
            ->artisan("get:weather $city->open_weather_id --http --user_id=1")
            ->assertExitCode(0);
        // Событие RefreshCityWeather выполняется
        Event::assertDispatched(RefreshCityWeather::class, 1);
    }
}

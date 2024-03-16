<?php

namespace Tests\Feature\Commands\OpenWeather;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
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
        $weatherData = $this->getWeatherForOneCity();
                
        Http::preventStrayRequests();
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($weatherData, 200),
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
        $this->assertEquals($weatherData['weather'][0]->description, $weather->weather_description);
        $this->assertEquals($weatherData['main']->temp, $weather->main_temp);
        $this->assertEquals(null, $weather->main_feels_like);
        $this->assertEquals($weatherData['main']->pressure, $weather->main_pressure);
        $this->assertEquals($weatherData['main']->humidity, $weather->main_humidity);
        $this->assertEquals($weatherData['visibility'], $weather->visibility);
        $this->assertEquals($weatherData['wind']->speed, $weather->wind_speed);
        $this->assertEquals($weatherData['wind']->deg, $weather->wind_deg);
        $this->assertEquals($weatherData['clouds']->all, $weather->clouds_all);
        
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
    
    public function test_weather_not_saved_if_too_early_to_submit_request_for_city(): void
    {
        Http::preventStrayRequests();
        
        // Выполняем посев городов
        $this->seedCities();
        // Берём один город, чтобы получить параметр команды
        $city = City::where('id', CitySeeder::ID_MOSCOW)->first();
        
        // Нет записей в таблице open_weather.weather
        $this->assertEquals(0, Weather::all()->count());
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);
        
        // При первом вызове команды запрос отправляется и погода сохраняется в базе
        $this
            ->artisan("get:weather $city->open_weather_id")
            ->doesntExpectOutput(trans('openweather.too_early_to_submit_request_for_this_city'))
            ->doesntExpectOutput('Выполнение команды прервано.')
            ->assertExitCode(0);
        // В таблице open_weather.weather появилась одна запись
        $this->assertEquals(1, Weather::all()->count());
        
        // При втором вызове команды запрос не отправляется (прошло меньше 10 минут)
        $this
            ->artisan("get:weather $city->open_weather_id")
            ->expectsOutput(trans('openweather.too_early_to_submit_request_for_this_city'))
            ->expectsOutput('Выполнение команды прервано.')
            ->assertExitCode(0);
        // Число записей в таблице open_weather.weather не изменилось
        $this->assertEquals(1, Weather::all()->count());
    }
    
    public function test_weather_not_saved_if_request_limit_exceeded_for_one_minute(): void
    {
        Http::preventStrayRequests();
        
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);
        
        // Нет записей в таблице open_weather.weather
        $this->assertEquals(0, Weather::all()->count());

        // Пока не превышен лимит запросов в минуту, происходит новое выполнение команды
        for ($i = 1; $i <= GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE; $i++) {
            // Создаем новый город
            $city = City::factory(['open_weather_id' => $i])->create();
            // Выполняется команда
            $this
                ->artisan("get:weather $city->open_weather_id")
                ->doesntExpectOutput(trans('openweather.request_limit_exceeded_for_one_minute'))
                ->doesntExpectOutput('Выполнение команды прервано.')
                ->assertExitCode(0);
            // В таблицу open_weather.weather добавляется новая запись
            $this->assertEquals($i, Weather::all()->count());
        }

        // Создаем новый город
        $city = City::factory(['open_weather_id' => GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE + 1])->create();
        // Выполняется команда
        $this
            ->artisan("get:weather $city->open_weather_id")
            ->expectsOutput(trans('openweather.request_limit_exceeded_for_one_minute'))
            ->expectsOutput('Выполнение команды прервано.')
            ->assertExitCode(0);
        // Число записей в таблице open_weather.weather не изменилось
        $this->assertEquals(GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE, Weather::all()->count());
    }
}

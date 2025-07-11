<?php

namespace Tests\Feature\Controllers\Project\Auth\Account;

use App\Events\RefreshCityWeather;
use App\Exceptions\OpenWeatherException;
use App\Models\Thesaurus\City;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\Support\Authentication;
use Tests\Support\Data\OpenWeather\OpenWeatherResponse;
use Tests\Support\User\UserCities;
use Tests\TestCase;

class UserWeatherTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities, OpenWeatherResponse;
    
    public function test_auth_can_get_weather(): void
    {
        $this->seedCitiesAndUsersWithWeather();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->get('userweather');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account/UserWeather')
                        ->has('errors', 0)
                        ->has('cities', 3)
                    // По алфавиту первым городом будет Москва
                        ->has('cities.0', fn (Assert $page) => 
                            $page->where('name', 'Москва')
                                ->has('weather', fn (Assert $page) =>
                                    $page->where('main_temp', '-3')
                                        ->etc()
                                )
                                ->has('timezone', fn (Assert $page) =>
                                    $page->where('name', 'Europe/Moscow')
                                    ->etc()
                                )
                                ->etc()
                        )
                        ->has('cities.1', fn (Assert $page) => 
                            $page->where('name', 'Новосибирск')
                                ->has('weather', fn (Assert $page) =>
                                    $page->where('main_temp', '22.91')
                                        ->etc()
                                )
                                ->has('timezone', fn (Assert $page) =>
                                    $page->where('name', 'Asia/Novosibirsk')
                                    ->etc()
                                )
                                ->etc()
                        )
                        ->has('cities.2', fn (Assert $page) => 
                            $page->where('name', 'Томск')
                                ->where('weather', null)
                                ->where('timezone', null)
                                ->etc()
                        )
            );
    }
    
    public function test_city_weather_can_be_refresh(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);
        
        Event::fake();
        
        // Посев без погоды
        $this->seedCitiesAndUsers();
        $city = City::where('id', CitySeeder::ID_NOVOSIBIRSK)->first();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->post("userweather/refresh/$city->id");
        
        // http-запрос был отправлен
        Http::assertSentCount(1);
        
        Event::assertDispatched(RefreshCityWeather::class, 1);
        
        $response
            ->assertOk();
    }
    
    public function test_city_weather_can_not_be_refresh_if_too_early_to_submit_request_for_city(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 200),
        ]);
        
        Event::fake();
        
        $this->expectException(OpenWeatherException::class);
        $this->expectExceptionMessage(trans('openweather.too_early_to_submit_request_for_this_city'));
        
        // Посев городов с погодой
        $this->seedCitiesAndUsersWithWeather();
        $city = City::where('id', CitySeeder::ID_NOVOSIBIRSK)->first();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->withoutExceptionHandling()->post("userweather/refresh/$city->id");
        
        // http-запрос не был отправлен
        Http::assertSentCount(0);
        // Событие Broadcasting не отправляется
        Event::assertNotDispatched(RefreshCityWeather::class);
        
        $response
            ->assertOk();
    }
    
    public function test_city_weather_can_not_be_refresh_if_the_response_does_not_have_200_http_status_code(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            "api.openweathermap.org/data/2.5/weather?*" => Http::response($this->getWeatherForOneCity(), 201),
        ]);
        
        Event::fake();
        
        // Посев без погоды
        $this->seedCitiesAndUsers();
        $city = City::where('id', CitySeeder::ID_NOVOSIBIRSK)->first();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->withoutExceptionHandling()->post("userweather/refresh/$city->id");
        
        // http-запрос был отправлен
        Http::assertSentCount(1);
        // Событие Broadcasting не отправляется
        Event::assertNotDispatched(RefreshCityWeather::class);
        
        $response
            ->assertOk();
    }
}

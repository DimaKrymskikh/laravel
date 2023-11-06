<?php

namespace Tests\Feature\Controllers\Project\Auth\Account;

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\Support\User\UserCities;
use Tests\TestCase;

class UserWeatherTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities;
    
    public function test_auth_can_get_weather(): void
    {
        $this->seedCitiesAndUsersWithWeather();
        
        $user = $this->getAuthUser();
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
                                ->has('weather_first', fn (Assert $page) =>
                                // Показаны последние данные по Москве
                                    $page->where('main_temp', '4')
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
                                ->has('weather_first', fn (Assert $page) =>
                                    $page->where('main_temp', '0.5')
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
                                ->where('weather_first', null)
                                ->where('timezone', null)
                                ->etc()
                        )
            );
    }
}

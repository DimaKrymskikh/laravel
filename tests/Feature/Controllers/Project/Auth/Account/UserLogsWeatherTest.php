<?php

namespace Tests\Feature\Controllers\Project\Auth\Account;

use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;
use Tests\Support\Authentication;
use Tests\Support\User\UserCities;

class UserLogsWeatherTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities;
    
    /**
     * A basic feature test example.
     */
    public function test_auth_can_get_weather_logs_for_one_city(): void
    {
        $this->seedCitiesAndUsersWithLogsWeather();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account/UserLogsWeather')
                        ->has('errors', 0)
                        ->has('weatherPage.data', 3)
                    // Проверка сортировки orderBy('created_at', 'desc')
                        ->has('weatherPage.data.0', fn (Assert $page) => 
                            $page->where('city_id', CitySeeder::ID_NOVOSIBIRSK)
                                ->where('main_temp', '0.5')
                                ->etc()
                        )
                        ->has('weatherPage.data.1', fn (Assert $page) => 
                            $page->where('city_id', CitySeeder::ID_NOVOSIBIRSK)
                                ->where('main_temp', '11')
                                ->etc()
                        )
                        ->has('weatherPage.data.2', fn (Assert $page) => 
                            $page->where('city_id', CitySeeder::ID_NOVOSIBIRSK)
                                ->where('main_temp', '22.91')
                                ->etc()
                        )
                        ->has('city', fn (Assert $page) => 
                            $page->where('name', 'Новосибирск')
                                ->etc()
                        )
            );
    }
}

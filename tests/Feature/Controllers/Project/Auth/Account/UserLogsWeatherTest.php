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
    
    public function test_assert_ok_if_request_parameters_are_valid(): void
    {
        $this->seedCitiesAndUsersWithLogsWeather();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $responseWithoutParameters = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK);
        $responseWithoutParameters->assertOk();

        $responseWithoutDate = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=3&number=10');
        $responseWithoutDate->assertOk();

        $responseEmptyDate = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=3&number=20&datefrom=&dateto=');
        $responseEmptyDate->assertOk();

        $responseAllParameters = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=3&number=20&datefrom=12.01.2024&dateto=29.02.2024');
        $responseAllParameters->assertOk();
    }
    
    public function test_assert_redirect_if_request_parameter_is_not_valid(): void
    {
        $this->seedCitiesAndUsersWithLogsWeather();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        // Параметр page не является целым
        $responsePage = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=&number=20');
        $responsePage->assertOk();
        // Параметр number не является целым
        $responseNumber = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=3&number=a');
        $responseNumber->assertOk();
        // Параметр datefrom не является датой
        $responseDatefrom = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=3&number=20&datefrom=111&dateto=01.02.2024');
        $responseDatefrom->assertOk();
        // Несуществующая дата в dateto
        $responseDateTo = $acting->get('userlogsweather/'.CitySeeder::ID_NOVOSIBIRSK.'?page=3&number=20&datefrom=12.01.2024&dateto=30.02.2024');
        $responseDateTo->assertOk();
    }
}

<?php

namespace Tests\Feature\Controllers\OpenWeather;

use App\Models\Thesaurus\City;
use App\Models\Thesaurus\Timezone;
use App\Models\OpenWeather\Weather;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_auth_can_get_weather(): void
    {
        $this->seed([
            \Database\Seeders\Thesaurus\TimezoneSeeder::class,
        ]);
        
        $tz = Timezone::where('name', 'Europe/Ulyanovsk')->first();
        
        $city = City::factory()
                ->count(3)
                ->state(new Sequence(
                        [],
                        [
                            'id' => 2,
                            'name' => 'TestCity2',
                            'open_weather_id' => 2,
                            'timezone_id' => $tz->id
                        ],
                        [
                            'id' => 3,
                            'name' => 'FirstCity',
                            'open_weather_id' => 23
                        ]
                    ))
                ->create();
        
        $time = Carbon::now();
        
        Weather::factory()
                ->count(3)
                ->state(new Sequence(
                        [
                            'city_id' => $city->get('id', 1),
                            'main_temp' => 10.5,
                            'created_at' => $time->toDateTimeString()
                        ],
                        [
                            'city_id' => $city->get('id', 1),
                            'main_temp' => -5.11,
                            'created_at' => $time->add(1, 'minute')->toDateTimeString()
                        ],
                        [
                            'city_id' => $city->get('id', 2),
                            'main_temp' => 22.14,
                            'created_at' => $time->toDateTimeString()
                        ],
                    ))
                ->create();
        
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->get('weather');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Weather')
                        ->has('errors', 0)
                        ->has('cities', 3)
                        ->has('cities.0', fn (Assert $page) => 
                            $page->where('name', 'FirstCity')
                                ->where('weather_first', null)
                                ->where('timezone', null)
                                ->etc()
                        )
                        ->has('cities.1', fn (Assert $page) => 
                            $page->where('name', 'TestCity')
                                ->has('weather_first', fn (Assert $page) =>
                                    $page->where('main_temp', '-5.11')
                                        ->etc()
                                )
                                ->where('timezone', null)
                                ->etc()
                        )
                        ->has('cities.2', fn (Assert $page) => 
                            $page->where('name', 'TestCity2')
                                ->has('weather_first', fn (Assert $page) =>
                                    $page->where('main_temp', '22.14')
                                    ->etc()
                                )
                                ->has('timezone', fn (Assert $page) =>
                                    $page->where('name', 'Europe/Ulyanovsk')
                                    ->etc()
                                )
                                ->etc()
                        )
            );
    }
}

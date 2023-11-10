<?php

namespace Tests\Feature\Controllers\Project\Auth\Content;

use App\Events\AddCityInWeatherList;
use App\Events\RemoveCityFromWeatherList;
use App\Models\Person\UserCity;
use App\Models\Thesaurus\City;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\Support\User\UserCities;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase, Authentication, UserCities;
    
    public function test_cities_page_displayed_for_auth_without_cities(): void
    {
        $acting = $this->actingAs($this->getUserBaseTestLogin());
        $response = $acting->get('cities');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Cities')
                        ->has('cities', 0)
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_cities_page_displayed_for_auth_with_cities(): void
    {
        City::factory()->count(2)
                ->state(new Sequence(
                    [],
                    [
                        'id' => 2,
                        'name' => 'TwoCity',
                        'open_weather_id' => 2
                    ],
                ))
                ->create();
        
        $acting = $this->actingAs($this->getUserBaseTestLogin());
        $response = $acting->get('cities');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Cities')
                        ->has('cities', 2)
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_auth_user_can_add_city(): void
    {
        Event::fake();
        
        $this->seedCitiesAndUsers();
        
        $user = $this->getAuthUser();
        
        // В начальный момент в списке пользователя 3 города
        $this->assertEquals(3, UserCity::where('user_id', $user->id)->count());
        
        $acting = $this->actingAs($user);
        $response = $acting->post('cities/addcity/' . CitySeeder::ID_OMSK);
        
        // Стало 4 города
        $this->assertEquals(4, UserCity::where('user_id', $user->id)->count());
        
        Event::assertDispatched(AddCityInWeatherList::class, 1);
        
        $response
            ->assertStatus(302)
            ->assertRedirect('cities');
    }
    
    public function test_auth_user_can_remove_city_if_correct_password(): void
    {
        Event::fake();
        
        $this->seedCitiesAndUsers();
        
        $user = $this->getAuthUser();
        
        // В начальный момент в списке пользователя 3 города
        $this->assertEquals(3, UserCity::where('user_id', $user->id)->count());
        
        $acting = $this->actingAs($user);
        $response = $acting->delete('cities/removecity/' . CitySeeder::ID_TOMSK, [
            'password' => 'BaseTestPassword0'
        ]);
        
        // Стало 2 города
        $this->assertEquals(2, UserCity::where('user_id', $user->id)->count());
        
        Event::assertDispatched(RemoveCityFromWeatherList::class, 1);
        
        $response
            ->assertStatus(302)
            ->assertRedirect('userweather');
    }
    
    public function test_auth_user_can_not_remove_city_if_uncorrect_password(): void
    {
        Event::fake();
        
        $this->seedCitiesAndUsers();
        
        $user = $this->getAuthUser();
        
        // В начальный момент в списке пользователя 3 города
        $this->assertEquals(3, UserCity::where('user_id', $user->id)->count());
        
        $acting = $this->actingAs($user);
        $response = $acting->delete('cities/removecity/' . CitySeeder::ID_TOMSK, [
            'password' => 'UnCorrectPassword0'
        ]);
        
        // Осталось 3 города
        $this->assertEquals(3, UserCity::where('user_id', $user->id)->count());
        
        Event::assertNotDispatched(RemoveCityFromWeatherList::class);
        
        $response->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
}

<?php

namespace Tests\Feature\Controllers\Project\Admin\Content;

use App\Models\Thesaurus\City;
use App\Providers\RouteServiceProvider;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Database\Seeders\Tests\Thesaurus\TimezoneSeeder;
use Database\Testsupport\Thesaurus\ThesaurusData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Database\Testsupport\Authentication;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase, Authentication, ThesaurusData;
    
    public function test_cities_page_displayed_for_admin_without_cities(): void
    {
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_CITIES);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Cities')
                        ->has('cities', 0)
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_cities_page_displayed_for_admin_with_cities(): void
    {
        $this->seedCities();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_CITIES);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Cities')
                        ->has('cities', City::all()->count())
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_cities_page_not_displayed_for_auth_not_admin(): void
    {
        $this->seedCities();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AuthTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_CITIES);

        $response
            ->assertStatus(403);
    }
    
    public function test_admin_can_add_city(): void
    {
        $this->seedCities();
        $nCities = City::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_CITIES, [
            'name' => 'ТестСити',
            'open_weather_id' => '777'
        ]);

        // Добавлен один фильм в таблицу 'thesaurus.cities'
        $this->assertEquals($nCities + 1, City::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
    
    public function test_admin_can_update_city(): void
    {
        $this->seedCities();
        $nCities = City::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_CITIES.'/'.CitySeeder::ID_MOSCOW, [
            'name' => 'НовыйСити',
        ]);

        $this->assertEquals($nCities, City::all()->count());
        $this->assertEquals('НовыйСити', City::find(CitySeeder::ID_MOSCOW)->name);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
    
    public function test_admin_can_delete_city(): void
    {
        $this->seedCities();
        $nCities = City::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_CITIES.'/'.CitySeeder::ID_MOSCOW, [
            'password' => 'AdminTestPassword1',
        ]);

        // Число городов в таблице 'thesaurus.cities' уменьшилось на 1
        $this->assertEquals($nCities - 1, City::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
    
    public function test_admin_can_not_delete_city_if_the_password_is_incorrect(): void
    {
        $this->seedCities();
        $nCities = City::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_CITIES.'/'.CitySeeder::ID_MOSCOW, [
            'password' => 'IncorrectPassword13',
        ]);

        // Число городов в таблице 'thesaurus.cities' не изменилось
        $this->assertEquals($nCities, City::all()->count());

        $response
            ->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
    
    public function test_admin_can_set_timezone_for_city(): void
    {
        $this->seedCities();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_CITIES.'/'.CitySeeder::ID_OMSK.'/timezone/'.TimezoneSeeder::ID_ASIA_NOVOSIBIRSK);
        
        $this->assertEquals(TimezoneSeeder::ID_ASIA_NOVOSIBIRSK, City::find(CitySeeder::ID_OMSK)->timezone_id);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
    
    public function test_admin_can_unset_timezone_for_city(): void
    {
        $this->seedCities();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_CITIES.'/'.CitySeeder::ID_MOSCOW.'/timezone/0');
        
        $this->assertEquals(null, City::find(CitySeeder::ID_MOSCOW)->timezone_id);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_CITIES);
    }
}

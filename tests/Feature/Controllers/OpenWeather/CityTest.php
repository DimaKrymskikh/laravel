<?php

namespace Tests\Feature\Controllers\OpenWeather;

use App\Models\Thesaurus\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_cities_page_displayed_for_admin_without_cities(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->get('cities');

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
        City::factory()->count(2)
                ->state(new Sequence(
                    [],
                    [
                        'name' => 'TwoCity',
                        'open_weather_id' => 2
                    ],
                ))
                ->create();
        
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->get('cities');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Cities')
                        ->has('cities', 2)
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_cities_page_not_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        $response = $acting->get('cities');

        $response
            ->assertStatus(403);
    }
    
    public function test_admin_can_add_city(): void
    {
        // В таблице 'thesaurus.cities' нет фильмов
        $this->assertEquals(0, City::all()->count());
        
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->post('cities', [
            'name' => 'ТестСити',
            'open_weather_id' => '777'
        ]);

        // Добавлен один фильм в таблицу 'thesaurus.cities'
        $this->assertEquals(1, City::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect('cities');
    }
    
    public function test_admin_can_update_city(): void
    {
        $city = City::factory()->create();
        
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->put("cities/$city->id", [
            'name' => 'НовыйСити',
        ]);

        $this->assertEquals('НовыйСити', City::find($city->id)->name);

        $response
            ->assertStatus(302)
            ->assertRedirect('cities');
    }
    
    public function test_admin_can_delete_city(): void
    {
        $city = City::factory()->create();
        // В таблице 'thesaurus.cities' находится 1 фильм
        $this->assertEquals(1, City::all()->count());
        
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->delete("cities/$city->id", [
            'password' => 'TestPassword7',
        ]);

        // В таблице 'thesaurus.cities' фильмов не осталось
        $this->assertEquals(0, City::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect('cities');
    }
}

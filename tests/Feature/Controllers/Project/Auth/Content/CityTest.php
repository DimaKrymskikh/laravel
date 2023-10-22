<?php

namespace Tests\Feature\Controllers\Project\Auth\Content;

use App\Models\Thesaurus\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\TestCase;

class CityTest extends TestCase
{
    use RefreshDatabase, Authentication;
    
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
}

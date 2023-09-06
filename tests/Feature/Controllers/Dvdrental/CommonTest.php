<?php

namespace Tests\Feature\Controllers\Dvdrental;

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommonTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_homepage_displayed_for_guest(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Home')
                        ->has('errors', 0)
            );
    }
    
    public function test_homepage_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);

        $response = $acting->get('/');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Home')
                        ->has('errors', 0)
            );
    }
    
    public function test_catalog_displayed_for_guest(): void
    {
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
        
        $response = $this->get('catalog?page=7&number=50&title=&description=');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Catalog')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                                $page->where('current_page', 7)
                                    ->has('data', 50) // Число фильмов на странице
                                    ->where('from', 301)
                                    ->where('last_page', 20)
                                    ->has('links')
                                    ->where('per_page', 50)
                                    ->where('to', 350)
                                    ->where('total', 1000)
                                    ->etc()
                            )
            );
    }
    
    public function test_catalog_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
        
        $response = $acting->get('catalog?number=10&title=ar&description=ep&page=2');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Catalog')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                                $page->where('current_page', 2)
                                    ->has('data', 9) // Число фильмов на странице
                                    ->where('from', 11)
                                    ->where('last_page', 2)
                                    ->has('links')
                                    ->where('per_page', 10)
                                    ->where('to', 19)
                                    ->where('total', 19)
                                    ->etc()
                            )
            );
    }
}

<?php

namespace Tests\Feature\Controllers\Project\Guest\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_films_list_displayed_for_guest(): void
    {
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
        
        $response = $this->get('guest/films?page=7&number=50&title=&description=');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Films')
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
}

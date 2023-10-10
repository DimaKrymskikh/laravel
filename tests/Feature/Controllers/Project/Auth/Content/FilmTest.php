<?php

namespace Tests\Feature\Controllers\Project\Auth\Content;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;
        
    public function test_films_list_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
        
        $response = $acting->get('films?number=10&title=ar&description=ep&page=2');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Films')
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

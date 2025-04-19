<?php

namespace Tests\Feature\Controllers\Project\Auth\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase, Authentication;
        
    public function test_films_list_displayed_for_auth(): void
    {
        $this->seedUsers();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->get('films?number=10&title_filter=ar&description_filter=ep&release_year_filter=2025&language_name_filter=ru&page=2');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Films')
            );
    }
}

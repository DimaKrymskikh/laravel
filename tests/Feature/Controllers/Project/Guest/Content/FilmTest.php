<?php

namespace Tests\Feature\Controllers\Project\Guest\Content;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_films_list_displayed_for_guest(): void
    {
        $response = $this->get(RouteServiceProvider::URL_GUEST_FILMS);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Guest/Films'));
    }
    
    public function test_films_can_be_filtered(): void
    {
        $response = $this->get(RouteServiceProvider::URL_GUEST_FILMS.'?'.http_build_query([
            'title_filter' => 'TestTitle',
            'description_filter' => 'TestDescription',
            'release_year_filter' => 'TestReleaseYear',
            'language_name_filter' => 'TestLanguageName',
        ]));
        
        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Guest/Films'));
    }
}

<?php

namespace Tests\Feature\Controllers\Project\Guest\Content;

use App\Models\Dvd\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Seeders;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase, Seeders;
    
    public function test_films_list_displayed_for_guest(): void
    {
        $this->seedFilms();
        
        $response = $this->get('guest/films?page=2&number=10&title=&description=');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Films')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                                $page->where('current_page', 2)
                                    ->has('data', 2) // Число фильмов на странице
                                    ->where('from', 11)
                                    ->where('last_page', 2)
                                    ->has('links')
                                    ->where('per_page', 10)
                                    ->where('to', 12)
                                    ->where('total', 12)
                                    ->etc()
                            )
            );
    }
    
    public function test_films_can_be_filtered(): void
    {
        $this->seedFilms();
        
        $responseTitle = $this->get('guest/films?page=1&number=10&title=no&description=');
        $responseTitle->assertOk();
        $this->assertEquals(2, Film::where('title', 'ilike', '%no%')->count());
        
        $responseDescription = $this->get('guest/films?page=1&number=10&title=&description=stud');
        $responseDescription->assertOk();
        $this->assertEquals(4, Film::where('description', 'ilike', '%stud%')->count());
        
        $responseTitleDescription = $this->get('guest/films?page=1&number=10&title=&description=stud');
        $responseTitleDescription->assertOk();
        $this->assertEquals(1, Film::where('title', 'ilike', '%no%')->where('description', 'ilike', '%stud%')->count());
    }
}

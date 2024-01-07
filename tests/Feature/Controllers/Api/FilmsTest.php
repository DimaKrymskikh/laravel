<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\Seeders;
use Tests\TestCase;

class FilmsTest extends TestCase
{
    use RefreshDatabase, Seeders;
    
    public function test_films_can_be_retrieved(): void
    {
        $this->seedFilms();
        
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->get('api/films-list');

        $response->assertStatus(200);
    }
    
    public function test_card_of_film_can_be_retrieved(): void
    {
        $this->seedFilms();
        
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->get('api/film-card/10');

        $response->assertStatus(200);
    }
}

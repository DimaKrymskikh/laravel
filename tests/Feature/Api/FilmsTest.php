<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FilmsTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_films_can_be_retrieved(): void
    {
        $this->before();
        
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->get('api/films-list');

        $response->assertStatus(200);
    }
    
    public function test_card_of_film_can_be_retrieved(): void
    {
        $this->before();
        
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->get('api/film-card/10');

        $response->assertStatus(200);
    }
    
    /**
     * Заполняем таблицы thesaurus.languages и dvd.films
     * 
     * @return void
     */
    private function before(): void
    {
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
    }
}

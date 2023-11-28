<?php

namespace Tests\Feature\Commands\database\Dvd;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Seeders;
use Tests\TestCase;

class CopyFilmsActorsTest extends TestCase
{
    use RefreshDatabase, Seeders;
    
    public function test_dvd_films_actors_table_data_copy_in_database_Copy_Dvd_FilmActorData(): void
    {
        Storage::fake('database');
        
        $this->seedFilmsAndActors();
        
        $this
            ->artisan('copy:dvd.films_actors')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Dvd/FilmActorData.php');
    }
}

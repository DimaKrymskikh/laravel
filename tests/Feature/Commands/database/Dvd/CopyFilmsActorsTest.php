<?php

namespace Tests\Feature\Commands\database\Dvd;

use Database\Testsupport\Dvd\DvdData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CopyFilmsActorsTest extends TestCase
{
    use RefreshDatabase, DvdData;
    
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

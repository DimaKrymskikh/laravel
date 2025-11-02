<?php

namespace Tests\Feature\Commands\database\Dvd;

use Database\Testsupport\Dvd\DvdData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CopyFilmsTest extends TestCase
{
    use RefreshDatabase, DvdData;
    
    public function test_dvd_films_table_data_copy_in_database_Copy_Dvd_FilmData(): void
    {
        Storage::fake('database');
        
        $this->seedFilms();
        
        $this
            ->artisan('copy:dvd.films')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Dvd/FilmData.php');
    }
}

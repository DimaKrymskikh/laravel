<?php

namespace Tests\Feature\Commands\database\Thesaurus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CopyCitiesTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_thesaurus_cities_table_data_copy_in_database_Copy_Thesaurus_CityData(): void
    {
        Storage::fake('database');
        
        $this->seed([
            \Database\Seeders\Thesaurus\CitySeeder::class,
        ]);
        
        $this
            ->artisan('copy:thesaurus.cities')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Thesaurus/CityData.php');
    }
}

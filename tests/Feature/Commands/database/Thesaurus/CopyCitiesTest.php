<?php

namespace Tests\Feature\Commands\database\Thesaurus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Seeders;
use Tests\TestCase;

class CopyCitiesTest extends TestCase
{
    use RefreshDatabase, Seeders;
    
    public function test_thesaurus_cities_table_data_copy_in_database_Copy_Thesaurus_CityData(): void
    {
        Storage::fake('database');
        
        $this->seedCities();
        
        $this
            ->artisan('copy:thesaurus.cities')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Thesaurus/CityData.php');
    }
}

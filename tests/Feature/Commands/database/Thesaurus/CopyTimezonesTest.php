<?php

namespace Tests\Feature\Commands\database\Thesaurus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CopyTimezonesTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_thesaurus_timezones_table_data_copy_in_database_Copy_Thesaurus_TimezoneData(): void
    {
        Storage::fake('database');
        
//        $this->seed([
//            \Database\Seeders\Thesaurus\TimezoneSeeder::class,
//        ]);
        
        $this
            ->artisan('copy:thesaurus.timezones')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Thesaurus/TimezoneData.php');
    }
}

<?php

namespace Tests\Feature\Commands\database\Dvd;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CopyActorsTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_dvd_actors_table_data_copy_in_database_Copy_Dvd_ActorData(): void
    {
        Storage::fake('database');
        
//        $this->seed([
//            \Database\Seeders\Dvd\ActorSeeder::class,
//        ]);
        
        $this
            ->artisan('copy:dvd.actors')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Dvd/ActorData.php');
    }
}

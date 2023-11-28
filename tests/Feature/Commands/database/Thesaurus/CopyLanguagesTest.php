<?php

namespace Tests\Feature\Commands\database\Thesaurus;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Seeders;
use Tests\TestCase;

class CopyLanguagesTest extends TestCase
{
    use RefreshDatabase, Seeders;
    
    public function test_thesaurus_languages_table_data_copy_in_database_Copy_Thesaurus_LanguageData(): void
    {
        Storage::fake('database');
        
        $this->seedLanguages();
        
        $this
            ->artisan('copy:thesaurus.languages')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('Thesaurus/LanguageData.php');
    }
}

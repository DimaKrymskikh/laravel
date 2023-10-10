<?php

namespace Tests\Feature\Commands\TimeZone;

use App\Models\Thesaurus\Timezone;
use Carbon\CarbonTimeZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CopyTimeZoneTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_timezones_can_be_copy_in_thesaurus_timezones_table(): void
    {
        $this
            ->artisan('copy:timezone')
            ->assertExitCode(0);
        
        $timezonesTable = Timezone::all();
        
        $timezonesPhp = CarbonTimeZone::listIdentifiers();
        
        $this->assertEquals(count($timezonesPhp), $timezonesTable->count());
    }
}

<?php

namespace Database\Seeders\Thesaurus;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Copy\Thesaurus\TimezoneData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'thesaurus.timezones';
        
        foreach ($this->getTimezones() as $tz) {
            DB::table($tableName)->insert([
                'id' => $tz->id,
                'name' => $tz->name,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getTimezones(): iterable
    {
        yield from (new TimezoneData())();
    }
}

<?php

namespace Database\Seeders\Thesaurus;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Copy\Thesaurus\CityData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'thesaurus.cities';
        
        foreach ($this->getCities() as $city) {
            DB::table($tableName)->insert([
                'id' => $city->id,
                'name' => $city->name,
                'open_weather_id' => $city->open_weather_id,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getCities(): iterable
    {
        yield from (new CityData())();
    }
}

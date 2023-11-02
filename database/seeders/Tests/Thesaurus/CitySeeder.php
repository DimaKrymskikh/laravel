<?php

namespace Database\Seeders\Tests\Thesaurus;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Seeders\Tests\Thesaurus\TimezoneSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    const ID_NOVOSIBIRSK = 1;
    const ID_MOSCOW = 2;
    const ID_OMSK = 3;
    const ID_TOMSK = 4;
    const ID_BARNAUL = 5;
    
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
                'timezone_id' => $city->timezone_id,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getCities(): array
    {
        return [
            (object) [
                'id' => self::ID_NOVOSIBIRSK,
                'name' => 'Новосибирск',
                'open_weather_id' => 1496747,
                'timezone_id' => TimezoneSeeder::ID_ASIA_NOVOSIBIRSK,
            ],
            (object) [
                'id' => self::ID_MOSCOW,
                'name' => 'Москва',
                'open_weather_id' => 524901,
                'timezone_id' => TimezoneSeeder::ID_EUROPE_MOSCOW,
            ],
            (object) [
                'id' => self::ID_OMSK,
                'name' => 'Омск',
                'open_weather_id' => 1496153,
                'timezone_id' => null,
            ],
            (object) [
                'id' => self::ID_TOMSK,
                'name' => 'Томск',
                'open_weather_id' => 1489425,
                'timezone_id' => null,
            ],
            (object) [
                'id' => self::ID_BARNAUL,
                'name' => 'Барнаул',
                'open_weather_id' => 1510853,
                'timezone_id' => TimezoneSeeder::ID_ASIA_BARNAUL,
            ],
        ];
    }
}

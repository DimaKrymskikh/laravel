<?php

namespace Database\Seeders\Tests\Thesaurus;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    const ID_EUROPE_DUBLIN = 1;
    const ID_AFRICA_DAKAR = 2;
    const ID_INDIAN_COMORO = 3;
    const ID_ASIA_BARNAUL = 4;
    const ID_PACIFIC_EASTER = 5;
    const ID_ASIA_KRASNOYARSK = 6;
    const ID_ASIA_NOVOSIBIRSK = 7;
    const ID_EUROPE_MOSCOW = 8;
    const ID_AMERICA_PANAMA = 9;
    
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
    
    private function getTimezones(): array
    {
        return [
            (object) [
                'id' => self::ID_EUROPE_DUBLIN,
                'name' => 'Europe/Dublin',
            ],
            (object) [
                'id' => self::ID_AFRICA_DAKAR,
                'name' => 'Africa/Dakar',
            ],
            (object) [
                'id' => self::ID_INDIAN_COMORO,
                'name' => 'Indian/Comoro',
            ],
            (object) [
                'id' => self::ID_ASIA_BARNAUL,
                'name' => 'Asia/Barnaul',
            ],
            (object) [
                'id' => self::ID_PACIFIC_EASTER,
                'name' => 'Pacific/Easter',
            ],
            (object) [
                'id' => self::ID_ASIA_KRASNOYARSK,
                'name' => 'Asia/Krasnoyarsk',
            ],
            (object) [
                'id' => self::ID_ASIA_NOVOSIBIRSK,
                'name' => 'Asia/Novosibirsk',
            ],
            (object) [
                'id' => self::ID_EUROPE_MOSCOW,
                'name' => 'Europe/Moscow',
            ],
            (object) [
                'id' => self::ID_AMERICA_PANAMA,
                'name' => 'America/Panama',
            ],
        ];
    }
}

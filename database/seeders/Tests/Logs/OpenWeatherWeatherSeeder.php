<?php

namespace Database\Seeders\Tests\Logs;

use Carbon\Carbon;
use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpenWeatherWeatherSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'logs.open_weather__weather';
        
        foreach ($this->getWeather() as $weather) {
            DB::table($tableName)->insert([
                'city_id' => $weather->city_id,
                'main_temp' => $weather->main_temp,
                'created_at' => $weather->created_at,
            ]);
        }
    }
    
    private function getWeather(): array
    {
        $time = Carbon::now();
        
        return [
            (object) [
                'city_id' => CitySeeder::ID_NOVOSIBIRSK,
                'main_temp' => 22.91,
                'created_at' => $time->toDateTimeString(),
            ],
            (object) [
                'city_id' => CitySeeder::ID_NOVOSIBIRSK,
                'main_temp' => 11,
                'created_at' => $time->add(1, 'minute')->toDateTimeString(),
            ],
            (object) [
                'city_id' => CitySeeder::ID_NOVOSIBIRSK,
                'main_temp' => 0.5,
                'created_at' => $time->add(2, 'minute')->toDateTimeString(),
            ],
            (object) [
                'city_id' => CitySeeder::ID_MOSCOW,
                'main_temp' => -3,
                'created_at' => $time->toDateTimeString(),
            ],
            (object) [
                'city_id' => CitySeeder::ID_MOSCOW,
                'main_temp' => 4,
                'created_at' => $time->add(1, 'minute')->toDateTimeString(),
            ],
            (object) [
                'city_id' => CitySeeder::ID_BARNAUL,
                'main_temp' => 2,
                'created_at' => $time->toDateTimeString(),
            ],
        ];
    }
}

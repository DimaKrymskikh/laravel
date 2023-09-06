<?php

namespace Database\Seeders\OpenWeather;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Copy\OpenWeather\WeatherData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeatherSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'open_weather.weather';
        
        foreach ($this->getWeather() as $weather) {
            DB::table($tableName)->insert([
                'id' => $weather->id,
                'city_id' => $weather->city_id,
                'weather_description' => $weather->weather_description,
                'main_temp' => $weather->main_temp,
                'main_feels_like' => $weather->main_feels_like,
                'main_pressure' => $weather->main_pressure,
                'main_humidity' => $weather->main_humidity,
                'visibility' => $weather->visibility,
                'wind_speed' => $weather->wind_speed,
                'wind_deg' => $weather->wind_deg,
                'clouds_all' => $weather->clouds_all,
                'created_at' => $weather->created_at,
            ]);
        
            $this->setSequenceMaxValue($tableName);
        }
    }
    
    private function getWeather(): iterable
    {
        yield from (new WeatherData())();
    }
}

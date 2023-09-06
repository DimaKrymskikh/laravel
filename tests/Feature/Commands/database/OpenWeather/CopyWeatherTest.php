<?php

namespace Tests\Feature\Commands\database\OpenWeather;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CopyWeatherTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_open_weather_weather_table_data_copy_in_database_Copy_OpenWeather_WeatherData(): void
    {
        Storage::fake('database');
        
//        $this->seed([
//            \Database\Seeders\Thesaurus\CitySeeder::class,
//            \Database\Seeders\OpenWeather\WeatherSeeder::class,
//        ]);
        
        $this
            ->artisan('copy:open_weather.weather')
            ->assertExitCode(0);
        
        Storage::disk('database')->assertExists('OpenWeather/WeatherData.php');
    }
}

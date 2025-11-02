<?php

namespace Database\Testsupport\Thesaurus;

trait ThesaurusData 
{
    private function seedLanguages(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
        ]);
    }
    
    private function seedTimezones(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
        ]);
    }
    
    private function seedCities(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
        ]);
    }
    
    private function seedCitiesAndWeather(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
            \Database\Seeders\Tests\OpenWeather\WeatherSeeder::class,
        ]);
    }
    
    private function seedCitiesAndLogsWeather(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
            \Database\Seeders\Tests\Logs\OpenWeatherWeatherSeeder::class,
        ]);
    }
}

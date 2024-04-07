<?php

namespace Tests\Support;

trait Seeders
{
    private function seedLanguages(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
        ]);
    }
    
    private function seedActors(): void
    {
        $this->seed([
                \Database\Seeders\Tests\Dvd\ActorSeeder::class,
        ]);
    }
    
    private function seedFilms(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmSeeder::class,
        ]);
    }
    
    private function seedFilmsAndActors(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Dvd\ActorSeeder::class,
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmActorSeeder::class,
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

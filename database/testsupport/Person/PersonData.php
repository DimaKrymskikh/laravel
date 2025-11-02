<?php

namespace Database\Testsupport\Person;

trait PersonData
{
    private function seedCitiesAndUsersWithWeather(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
            \Database\Seeders\Tests\Person\UserSeeder::class,
            \Database\Seeders\Tests\Person\UserCitySeeder::class,
            \Database\Seeders\Tests\OpenWeather\WeatherSeeder::class,
        ]);
    }

    private function seedCitiesAndUsersWithLogsWeather(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
            \Database\Seeders\Tests\Person\UserSeeder::class,
            \Database\Seeders\Tests\Person\UserCitySeeder::class,
            \Database\Seeders\Tests\Logs\OpenWeatherWeatherSeeder::class,
        ]);
    }
    
    private function seedCitiesAndUsers(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
            \Database\Seeders\Tests\Person\UserSeeder::class,
            \Database\Seeders\Tests\Person\UserCitySeeder::class,
        ]);
    }
    
    private function seedUserFilms(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Person\UserSeeder::class,
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmSeeder::class,
            \Database\Seeders\Tests\Person\UserFilmSeeder::class,
        ]);
    }
    
    private function seedUserFilmsWithActors(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Person\UserSeeder::class,
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Tests\Dvd\ActorSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmSeeder::class,
            \Database\Seeders\Tests\Person\UserFilmSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmActorSeeder::class,
        ]);
    }
}

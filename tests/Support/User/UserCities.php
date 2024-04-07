<?php

namespace Tests\Support\User;

trait UserCities
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
}

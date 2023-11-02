<?php

namespace Tests\Support\User;

trait UserCities
{
    private function seedCitiesWithWeatherForAuthUser(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Tests\Thesaurus\CitySeeder::class,
            \Database\Seeders\Tests\Person\UserSeeder::class,
            \Database\Seeders\Tests\Person\UserCitySeeder::class,
            \Database\Seeders\Tests\OpenWeather\WeatherSeeder::class,
        ]);
    }
}

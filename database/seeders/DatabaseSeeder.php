<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\Thesaurus\TimezoneSeeder::class,
            \Database\Seeders\Thesaurus\CitySeeder::class,
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\ActorSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
            \Database\Seeders\Dvd\FilmActorSeeder::class,
            \Database\Seeders\OpenWeather\WeatherSeeder::class,
            \Database\Seeders\Person\BaseTestUserSeeder::class,
        ]);
    }
}

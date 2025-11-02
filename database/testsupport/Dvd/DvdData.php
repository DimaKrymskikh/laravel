<?php

namespace Database\Testsupport\Dvd;

trait DvdData
{
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
}

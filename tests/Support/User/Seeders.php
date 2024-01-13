<?php

namespace Tests\Support\User;

trait Seeders
{
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

<?php

namespace Tests\Support;

trait Seeders
{
    private function seedFilms(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Tests\Dvd\FilmSeeder::class,
        ]);
    }
}

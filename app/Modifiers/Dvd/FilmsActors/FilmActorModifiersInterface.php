<?php

namespace App\Modifiers\Dvd\FilmsActors;

use App\Models\Dvd\FilmActor;

interface FilmActorModifiersInterface
{
    public function save(FilmActor $filmActor, int $filmId, int $actorId): void;
    
    public function delete(int $filmId, int $actorId): void;
}

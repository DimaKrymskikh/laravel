<?php

namespace App\Modifiers\Dvd\FilmsActors;

use App\Models\Dvd\FilmActor;

final class FilmActorModifiers implements FilmActorModifiersInterface
{
    public function save(FilmActor $filmActor, int $filmId, int $actorId): void
    {
        $filmActor->film_id = $filmId;
        $filmActor->actor_id = $actorId;
        $filmActor->save();
    }
    
    public function delete(int $filmId, int $actorId): void
    {
        FilmActor::where('actor_id', $actorId)
                ->where('film_id', $filmId)
                ->delete();
    }
}

<?php

namespace App\Modifiers\Dvd\FilmsActors;

use App\Models\Dvd\FilmActor;
use App\Services\Database\Dvd\Dto\FilmActorDto;

final class FilmActorModifiers implements FilmActorModifiersInterface
{
    public function save(FilmActorDto $dto): void
    {
        FilmActor::insert([
            'film_id' => $dto->filmId,
            'actor_id' => $dto->actorId,
        ]);
    }
    
    public function remove(FilmActorDto $dto): void
    {
        FilmActor::where('actor_id', $dto->actorId)
                ->where('film_id', $dto->filmId)
                ->delete();
    }
}

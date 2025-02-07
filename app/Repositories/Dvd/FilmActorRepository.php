<?php

namespace App\Repositories\Dvd;

use App\Models\Dvd\FilmActor;
use Illuminate\Database\Eloquent\Collection;

final class FilmActorRepository implements FilmActorRepositoryInterface
{
    public function exists(int $filmId, int $actorId): bool
    {
        return FilmActor::where('film_id', $filmId)
                ->where('actor_id', $actorId)
                ->exists();
    }
    
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
    
    public function getByFilmId(int $filmId): Collection
    {
        return FilmActor::where('film_id', $filmId)->get();
    }
}

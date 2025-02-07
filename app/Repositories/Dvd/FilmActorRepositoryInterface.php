<?php

namespace App\Repositories\Dvd;

use App\Models\Dvd\FilmActor;
use Illuminate\Database\Eloquent\Collection;

interface FilmActorRepositoryInterface
{
    public function exists(int $filmId, int $actorId): bool;
    
    public function save(FilmActor $filmActor, int $filmId, int $actorId): void;
    
    public function delete(int $filmId, int $actorId): void;
    
    public function getByFilmId(int $filmId): Collection;
}

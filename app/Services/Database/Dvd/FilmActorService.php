<?php

namespace App\Services\Database\Dvd;

use App\Models\Dvd\FilmActor;
use App\Queries\Dvd\ActorQueries;
use App\Queries\Dvd\FilmActorQueries;
use App\Queries\Dvd\FilmQueries;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final class FilmActorService
{
    public function __construct(
            private ActorQueries $actorQueries,
            private FilmActorQueries $filmActorQueries,
            private FilmQueries $filmQueries,
    ) {
    }
    
    public function create(int $filmId, int $actorId): void
    {
        if ($this->filmActorQueries->checkFilmActor($filmId, $actorId)) {
            // Если пара существует, выбрасываем исключение
            $film = $this->filmQueries->getFilmById($filmId);
            $actor = $this->actorQueries->getActorById($actorId);
            throw ValidationException::withMessages([
                'message' => trans("attr.film.contains.actor", [
                    'film' => $film->title,
                    'actor' => "$actor->first_name $actor->last_name",
                ]),
            ]);
        }
        
        $filmActor = new FilmActor();
        $filmActor->film_id = $filmId;
        $filmActor->actor_id = $actorId;
        $filmActor->save();
    }
    
    public function getActorsListByFilmId(int $filmId): Collection
    {
        return $this->filmActorQueries->queryActorsListByFilmId($filmId)->get();
    }
    
    public function delete(int $filmId, int $actorId): void
    {
        $this->filmActorQueries->queryFilmActor($filmId, $actorId)->delete();
    }
}

<?php

namespace App\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Models\Dvd\FilmActor;
use App\Modifiers\Dvd\FilmsActors\FilmActorModifiersInterface;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

final class FilmActorService
{
    public function __construct(
            private ActorQueriesInterface $actorQueries,
            private FilmActorModifiersInterface $filmActorModifiers,
            private FilmActorQueriesInterface $filmActorQueries,
            private FilmQueriesInterface $filmQueries,
    ) {
    }
    
    public function create(int $filmId, int $actorId): void
    {
        if ($this->filmActorQueries->exists($filmId, $actorId)) {
            // Если пара существует, выбрасываем исключение
            $filmTitle = $this->filmQueries->getById($filmId)->title;
            $actor = $this->actorQueries->getById($actorId);
            $name = "$actor->->first_name $actor->last_name";
            throw new DatabaseException("Фильм '$filmTitle' уже содержит актёра $name");
        }
        
        $this->filmActorModifiers->save(new FilmActor(), $filmId, $actorId);
    }
    
    public function getActorsListByFilmId(int $filmId): Collection
    {
        return $this->filmActorQueries->getByFilmId($filmId);
    }
    
    public function delete(int $filmId, int $actorId): void
    {
        if(!$this->filmActorQueries->exists($filmId, $actorId)) {
            throw new DatabaseException("В таблице 'dvd.films_actors' нет записи с film_id=$filmId и actor_id=$actorId");
        }
        
        $this->filmActorModifiers->delete($filmId, $actorId);
    }
}

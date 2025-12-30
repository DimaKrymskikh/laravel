<?php

namespace App\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Modifiers\Dvd\FilmsActors\FilmActorModifiersInterface;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Dvd\FilmsActors\FilmActorQueriesInterface;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Support\Collections\Dvd\FilmActorCollection;

final class FilmActorService
{
    public function __construct(
            private ActorQueriesInterface $actorQueries,
            private FilmActorModifiersInterface $filmActorModifiers,
            private FilmActorQueriesInterface $filmActorQueries,
            private FilmQueriesInterface $filmQueries,
    ) {
    }
    
    public function create(FilmActorDto $dto): void
    {
        if ($this->filmActorQueries->exists($dto)) {
            // Если пара существует, выбрасываем исключение
            $filmTitle = $this->filmQueries->getById($dto->filmId)->title;
            $actor = $this->actorQueries->getById($dto->actorId);
            $name = "$actor->->first_name $actor->last_name";
            throw new DatabaseException("Фильм '$filmTitle' уже содержит актёра $name");
        }
        
        $this->filmActorModifiers->save($dto);
    }
    
    public function getActorsListByFilmId(int $filmId): FilmActorCollection
    {
        return $this->filmActorQueries->getByFilmId($filmId);
    }
    
    public function delete(FilmActorDto $dto): void
    {
        if(!$this->filmActorQueries->exists($dto)) {
            throw new DatabaseException("В таблице 'dvd.films_actors' нет записи с film_id=$dto->filmId и actor_id=$dto->actorId");
        }
        
        $this->filmActorModifiers->remove($dto);
    }
}

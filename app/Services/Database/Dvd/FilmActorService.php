<?php

namespace App\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Modifiers\Dvd\FilmActorModifiers;
use App\Queries\Dvd\ActorQueries;
use App\Queries\Dvd\FilmQueries;
use App\Queries\Dvd\FilmActorQueries;
use App\Services\Database\Dvd\Dto\FilmActorDto;
use App\Services\ServiceManagerInterface;
use App\Support\Collections\Dvd\FilmActorCollection;

final class FilmActorService
{
    private ActorQueries $actorQueries;
    private FilmActorModifiers $filmActorModifiers;
    private FilmActorQueries $filmActorQueries;
    private FilmQueries $filmQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->actorQueries = $this->serviceManager->getQueriesOrModifiers(ActorQueries::class);
        $this->filmActorModifiers = $this->serviceManager->getQueriesOrModifiers(FilmActorModifiers::class);
        $this->filmActorQueries = $this->serviceManager->getQueriesOrModifiers(FilmActorQueries::class);
        $this->filmQueries = $this->serviceManager->getQueriesOrModifiers(FilmQueries::class);
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

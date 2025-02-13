<?php

namespace App\Services\Database\Dvd;

use App\Exceptions\DatabaseException;
use App\Models\Dvd\FilmActor;
use App\Repositories\Dvd\ActorRepositoryInterface;
use App\Repositories\Dvd\FilmActorRepositoryInterface;
use App\Repositories\Dvd\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class FilmActorService
{
    public function __construct(
            private ActorRepositoryInterface $actorRepository,
            private FilmActorRepositoryInterface $filmActorRepository,
            private FilmRepositoryInterface $filmRepository,
    ) {
    }
    
    public function create(int $filmId, int $actorId): void
    {
        if ($this->filmActorRepository->exists($filmId, $actorId)) {
            // Если пара существует, выбрасываем исключение
            $filmTitle = $this->filmRepository->getById($filmId)->title;
            $actor = $this->actorRepository->getById($actorId);
            $name = "$actor->->first_name $actor->last_name";
            throw new DatabaseException("Фильм '$filmTitle' уже содержит актёра $name");
        }
        
        $this->filmActorRepository->save(new FilmActor(), $filmId, $actorId);
    }
    
    public function getActorsListByFilmId(int $filmId): Collection
    {
        return $this->filmActorRepository->getByFilmId($filmId);
    }
    
    public function delete(int $filmId, int $actorId): void
    {
        if(!$this->filmActorRepository->exists($filmId, $actorId)) {
            throw new DatabaseException("В таблице 'dvd.films_actors' нет записи с film_id=$filmId и actor_id=$actorId");
        }
        
        $this->filmActorRepository->delete($filmId, $actorId);
    }
}

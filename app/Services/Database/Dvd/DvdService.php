<?php

namespace App\Services\Database\Dvd;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Actor;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\ActorModifiers;
use App\Modifiers\Dvd\FilmModifiers;
use App\Queries\Dvd\ActorQueries;
use App\Queries\Dvd\FilmQueries;

abstract class DvdService
{
    public const NOT_RECORD_WITH_ID_IN_DVD_ACTORS = "В таблице 'dvd.actors' нет записи с id=%d";
    public const NOT_RECORD_WITH_ID_IN_DVD_FILMS = "В таблице 'dvd.films' нет записи с id=%d";
    
    protected ActorModifiers $actorModifiers;
    protected FilmModifiers $filmModifiers;
    protected FilmQueries $filmQueries;
    protected ActorQueries $actorQueries;
    
    public function __construct(
            protected readonly ServiceManagerInterface $serviceManager
    ) {
        $this->actorModifiers = $this->serviceManager->getQueriesOrModifiers(ActorModifiers::class);
        $this->filmModifiers = $this->serviceManager->getQueriesOrModifiers(FilmModifiers::class);
        $this->actorQueries = $this->serviceManager->getQueriesOrModifiers(ActorQueries::class);
        $this->filmQueries = $this->serviceManager->getQueriesOrModifiers(FilmQueries::class);
    }
    
    /**
     * По id таблицы 'dvd.actors' извлекает запись и создаёт ORM-модель.
     * Если в таблице нет записи, будет выброшено исключение DatabaseException.
     * 
     * @param int $id
     * @return Actor
     */
    public function getActorById(int $id): Actor
    {
        return $this->actorQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_DVD_ACTORS, $id));
    }
    
    /**
     * По id таблицы 'dvd.films' извлекает запись и создаёт ORM-модель.
     * Если в таблице нет записи, будет выброшено исключение DatabaseException.
     * 
     * @param int $id
     * @return Film
     */
    public function getFilmById(int $id): Film
    {
        return $this->filmQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_DVD_FILMS, $id));
    }
}

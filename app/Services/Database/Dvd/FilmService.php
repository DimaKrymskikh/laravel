<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Support\Collections\Dvd\FilmCollection;

final class FilmService
{
    public function __construct(
            private FilmQueriesInterface $filmQueries,
            private FilmModifiersInterface $filmModifiers,
    ) {
    }
    
    public function create(FilmDto $dto): Film
    {
        $film = new Film();
        $film->title = $dto->title;
        $film->description = $dto->description;
        $film->release_year = $dto->releaseYear->value;
        
        $this->filmModifiers->save($film);
        
        return $film;
    }
    
    public function update(string $field, string|null $value, int $filmId): Film
    {
        $film = $this->filmQueries->getById($filmId);
        $film->$field = $value;
        
        $this->filmModifiers->save($film);
        
        return $film;
    }
    
    public function delete(int $filmId): void
    {
        if (!$this->filmQueries->exists($filmId)) {
            throw new DatabaseException(sprintf(FilmQueriesInterface::NOT_RECORD_WITH_ID, $filmId));
        }
        
        $this->filmModifiers->delete($filmId);
    }
    
    public function getFilmCard(int $filmId): Film
    {
        return $this->filmQueries->getByIdWithActors($filmId);
    }
    
    public function getFilmsList(FilmFilterDto $filmFilterDto): FilmCollection
    {
        return $this->filmQueries->getListWithFilter($filmFilterDto);
    }
    
    public function getActorsList(int $filmId): Film
    {
        return $this->filmQueries->getByIdWithActors($filmId);
    }
}

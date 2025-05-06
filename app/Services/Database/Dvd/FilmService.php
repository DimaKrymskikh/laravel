<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

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
        $this->filmModifiers->save($film, $dto);
        
        return $film;
    }
    
    public function update(string $field, string|null $value, int $filmId): Film
    {
        $film = $this->filmQueries->getById($filmId);
        $this->filmModifiers->saveField($film, $field, $value);
        
        return $film;
    }
    
    public function delete(int $filmId): void
    {
        if (!$this->filmQueries->exists($filmId)) {
            throw new DatabaseException(sprintf(FilmQueriesInterface::NOT_RECORD_WITH_ID, $filmId));
        }
        
        $this->filmModifiers->delete($filmId);
    }
    
    public function getFilmById($filmId): Film
    {
        return $this->filmQueries->getById($filmId);
    }
    
    public function getFilmCard(int $filmId): Film
    {
        return $this->filmQueries->getByIdWithActors($filmId);
    }
    
    public function getFilmsList(FilmFilterDto $filmFilterDto): Collection
    {
        return $this->filmQueries->getList($filmFilterDto);
    }
    
    public function getActorsList(int $filmId): Film
    {
        return $this->filmQueries->getByIdWithActors($filmId);
    }
}

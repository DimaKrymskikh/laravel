<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Support\Collections\Dvd\FilmCollection;

final class FilmService extends DvdService
{
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
        $film = $this->getFilmById($filmId);
        $film->$field = $value;
        
        $this->filmModifiers->save($film);
        
        return $film;
    }
    
    public function delete(int $filmId): void
    {
        if (!$this->filmQueries->exists($filmId)) {
            throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_DVD_FILMS, $filmId));
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

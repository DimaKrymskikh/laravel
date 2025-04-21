<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use App\Repositories\Dvd\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class FilmService
{
    public function __construct(
            private FilmModifiersInterface $filmModifiers,
            private FilmRepositoryInterface $filmRepository,
    ) {
    }
    
    public function create(FilmDto $dto): Film
    {
        $film = new Film();
        $this->filmModifiers->save($film, $dto);
        
        return $film;
    }
    
    public function update(string $field, ?string $value, int $filmId): Film
    {
        $film = $this->filmRepository->getById($filmId);
        $this->filmModifiers->saveField($film, $field, $value);
        
        return $film;
    }
    
    public function delete(int $filmId): void
    {
        if (!$this->filmRepository->exists($filmId)) {
            throw new DatabaseException("В таблице 'dvd.films' нет записи с id=$filmId.");
        }
        
        $this->filmModifiers->delete($filmId);
    }
    
    public function getFilmById($filmId): Film
    {
        return $this->filmRepository->getById($filmId);
    }
    
    public function getFilmCard(int $filmId): Film
    {
        return $this->filmRepository->getByIdWithActors($filmId);
    }
    
    public function getFilmsList(FilmFilterDto $filmFilterDto): Collection
    {
        return $this->filmRepository->getList($filmFilterDto);
    }
    
    public function getActorsList(int $filmId): Film
    {
        return $this->filmRepository->getByIdWithActors($filmId);
    }
}

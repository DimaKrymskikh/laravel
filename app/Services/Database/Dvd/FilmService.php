<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Film;
use App\Repositories\Dvd\FilmRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmService
{
    public function __construct(
        private FilmRepositoryInterface $filmRepository,
    ) {
    }
    
    public function create(FilmDto $dto): Film
    {
        $film = new Film();
        $this->filmRepository->save($film, $dto);
        
        return $film;
    }
    
    public function update(string $field, ?string $value, int $filmId): Film
    {
        $film = $this->filmRepository->getById($filmId);
        $this->filmRepository->saveField($film, $field, $value);
        
        return $film;
    }
    
    public function delete(int $filmId): void
    {
        $this->filmRepository->delete($filmId);
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
    
    public function getFilmsListForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        return $this->filmRepository->getListForPage($paginatorDto, $filmFilterDto);
    }
    
    public function getFilmsListByUserIdForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): LengthAwarePaginator
    {
        return $this->filmRepository->getListByUserIdForPage($paginatorDto, $filmFilterDto, $userId);
    }
    
    public function getFilmsListWithAvailable(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): LengthAwarePaginator
    {
        return $this->filmRepository->getListForPageWithAvailable($paginatorDto, $filmFilterDto, $userId);
    }
    
    public function getFilmsListWithActorsForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        return $this->filmRepository->getListForPageWithActors($paginatorDto, $filmFilterDto);
    }
    
    public function getActorsList(int $filmId): Film
    {
        return $this->filmRepository->getByIdWithActors($filmId);
    }
}

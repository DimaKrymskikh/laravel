<?php

namespace App\Services\Database\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Database\Dvd\FilmDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Dvd\Film;
use App\Queries\Dvd\FilmQueries;
use App\Services\Database\BaseDatabaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

final class FilmService extends BaseDatabaseService
{
    public function __construct(
        private FilmQueries $filmQueries,
    ) {
    }
    
    public function create(FilmDto $dto): Film
    {
        $film = new Film();
        $film->title = $dto->title;
        $film->description = $dto->description;
        $film->release_year = $dto->releaseYear->value;
        $film->save();
        
        return $film;
    }
    
    public function update(string $field, ?string $value, int $filmId): Film
    {
        $film = $this->filmQueries->getFilmById($filmId);
        $film->$field = $value;
        $film->save();
        
        return $film;
    }
    
    public function delete(int $filmId): void
    {
        $this->filmQueries->delete($filmId);
    }
    
    public function getFilmCard(int $filmId): Film
    {
        return $this->filmQueries->queryFilmCard($filmId)->first();
    }
    
    public function getAllFilmsList(FilmFilterDto $filmFilterDto): Collection
    {
        return $this->filmQueries->queryFilmsList($filmFilterDto)->limit(BaseDatabaseService::DEFAULT_LIMIT)->get();
    }
    
    public function getFilmsListForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $query = $this->filmQueries->queryFilmsList($filmFilterDto);
        
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getUserFilmsListForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): LengthAwarePaginator
    {
        $query = $this->filmQueries->queryUserFilmsList($filmFilterDto, $userId);
        
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getFilmsListWithAvailable(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int $userId): LengthAwarePaginator
    {
        $query = $this->filmQueries->queryFilmsListWithAvailable($filmFilterDto, $userId);
                
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getFilmsListWithActorsForPage(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $query = $this->filmQueries->queryFilmsListWithActors($filmFilterDto);
                
        return $this->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    public function getActorsList(int $filmId): Film
    {
        return $this->filmQueries->queryActorsList($filmId)->first();
    }
    
    private function paginate(Builder $query, PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'title_filter' => $filmFilterDto->title,
                    'description_filter' => $filmFilterDto->description,
                    'release_year_filter' => (string) $filmFilterDto->releaseYear,
                ]);
    }
}

<?php

namespace App\Repositories\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use Illuminate\Database\Eloquent\Collection;

interface FilmRepositoryInterface
{
    public function exists(int $filmId): bool;
    
    public function count(FilmFilterDto $dto): int;
    
    public function getById(int $filmId): Film;
    
    public function getByIdWithActors(int $filmId): Film;
    
    public function getList(FilmFilterDto $dto): Collection;
    
    public function getRowNumbers(): Collection;
}

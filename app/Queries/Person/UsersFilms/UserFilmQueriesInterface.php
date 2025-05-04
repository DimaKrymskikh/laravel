<?php

namespace App\Queries\Person\UsersFilms;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;

interface UserFilmQueriesInterface
{
    public function exists(int $userId, int $filmId): bool;
    
    public function count(FilmFilterDto $dto, int $userId): int;
}

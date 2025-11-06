<?php

namespace App\Queries\Person\UsersFilms;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Services\Database\Person\Dto\UserFilmDto;

interface UserFilmQueriesInterface
{
    public function exists(UserFilmDto $dto): bool;
    
    public function count(FilmFilterDto $dto, int $userId): int;
}

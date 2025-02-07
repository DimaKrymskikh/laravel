<?php

namespace App\Repositories\Person;

use App\Models\Person\UserFilm;

interface UserFilmRepositoryInterface
{
    public function exists(int $userId, int $filmId): bool;
    
    public function save(UserFilm $userFilm, int $userId, int $filmId): void;
    
    public function delete(int $userId, int $filmId): void;
}

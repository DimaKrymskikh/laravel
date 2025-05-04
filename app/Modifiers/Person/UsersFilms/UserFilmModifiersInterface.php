<?php

namespace App\Modifiers\Person\UsersFilms;

use App\Models\Person\UserFilm;

interface UserFilmModifiersInterface
{
    public function save(UserFilm $userFilm, int $userId, int $filmId): void;
    
    public function delete(int $userId, int $filmId): void;
}

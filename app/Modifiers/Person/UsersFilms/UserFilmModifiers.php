<?php

namespace App\Modifiers\Person\UsersFilms;

use App\Models\Person\UserFilm;

final class UserFilmModifiers implements UserFilmModifiersInterface
{
    public function save(UserFilm $userFilm, int $userId, int $filmId): void
    {
        $userFilm->user_id = $userId;
        $userFilm->film_id = $filmId;
        $userFilm->save();
    }
    
    public function delete(int $userId, int $filmId): void
    {
        UserFilm::where('user_id', '=', $userId)
            ->where('film_id', '=', $filmId)
            ->delete();
    }
}

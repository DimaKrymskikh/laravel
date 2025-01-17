<?php

namespace App\Queries\Person;

use App\Models\Person\UserFilm;
use Illuminate\Database\Eloquent\Builder;

class UserFilmQueries
{
    public function checkUserFilm(int $userId, int $filmId): bool
    {
        return UserFilm::where('user_id', '=', $userId)
                ->where('film_id', '=', $filmId)
                ->exists();
    }
    
    public function getUserFilm(int $userId, int $filmId): Builder
    {
        return UserFilm::where('user_id', '=', $userId)
                ->where('film_id', '=', $filmId);
    }
}

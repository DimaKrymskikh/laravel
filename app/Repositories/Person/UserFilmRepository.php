<?php

namespace App\Repositories\Person;

use App\Models\Person\UserFilm;

final class UserFilmRepository implements UserFilmRepositoryInterface
{
    public function exists(int $userId, int $filmId): bool
    {
        return UserFilm::where('user_id', $userId)
                ->where('film_id', $filmId)
                ->exists();
    }
    
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

<?php

namespace App\Modifiers\Person\UsersFilms;

use App\Models\Person\UserFilm;
use App\Services\Database\Person\Dto\UserFilmDto;

final class UserFilmModifiers implements UserFilmModifiersInterface
{
    public function save(UserFilmDto $dto): void
    {
        UserFilm::insert([
            'user_id' => $dto->userId,
            'film_id' => $dto->filmId
        ]);
    }
    
    public function remove(UserFilmDto $dto): void
    {
        UserFilm::where('user_id', '=', $dto->userId)
            ->where('film_id', '=', $dto->filmId)
            ->delete();
    }
}

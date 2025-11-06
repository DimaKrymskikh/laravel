<?php

namespace App\Modifiers\Person\UsersFilms;

use App\Services\Database\Person\Dto\UserFilmDto;

interface UserFilmModifiersInterface
{
    public function save(UserFilmDto $dto): void;
    
    public function remove(UserFilmDto $dto): void;
}

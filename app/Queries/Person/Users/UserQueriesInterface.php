<?php

namespace App\Queries\Person\Users;

use App\Models\User;
use App\Queries\DBqueriesInterface;

interface UserQueriesInterface extends DBqueriesInterface
{
    /**
     * Получить пользователя из таблицы 'person.users' по его id.
     * 
     * @param int $id
     * @return User
     */
    public function getById(int $id): User;
}

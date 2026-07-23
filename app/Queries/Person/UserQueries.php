<?php

namespace App\Queries\Person;

use App\Models\User;
use App\Queries\DBqueries;

class UserQueries extends DBqueries
{
    /**
     * Получить пользователя из таблицы 'person.users' по его id.
     * 
     * @param int $id
     * @return User
     */
    public function getById(int $id): User
    {
        return User::find($id);
    }
}

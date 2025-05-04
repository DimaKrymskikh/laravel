<?php

namespace App\Queries\Person\Users;

use App\Models\User;

final class UserQueries implements UserQueriesInterface
{
    public function getById(int $id): User
    {
        return User::find($id);
    }
}

<?php

namespace App\Queries\Person\Users;

use App\Models\User;
use App\Queries\DBqueries;

final class UserQueries extends DBqueries implements UserQueriesInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getById(int $id): User
    {
        return User::find($id);
    }
}

<?php

namespace App\Queries\Person\Users;

use App\Models\User;

interface UserQueriesInterface
{
    public function getById(int $id): User;
}

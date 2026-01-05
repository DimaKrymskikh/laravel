<?php

namespace App\Queries\Person\Users;

use App\Models\User;
use App\Queries\DBqueriesInterface;

interface UserQueriesInterface extends DBqueriesInterface
{
    public function getById(int $id): User;
}

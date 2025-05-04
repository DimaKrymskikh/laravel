<?php

namespace App\Queries\Person\UsersCities;

interface UserCityQueriesInterface
{
    public function exists(int $userId, int $cityId): bool;
}

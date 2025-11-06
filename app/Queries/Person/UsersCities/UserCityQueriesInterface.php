<?php

namespace App\Queries\Person\UsersCities;

use App\Services\Database\Person\Dto\UserCityDto;

interface UserCityQueriesInterface
{
    public function exists(UserCityDto $dto): bool;
}

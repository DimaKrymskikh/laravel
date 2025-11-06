<?php

namespace App\Queries\Person\UsersCities;

use App\Models\Person\UserCity;
use App\Services\Database\Person\Dto\UserCityDto;

final class UserCityQueries implements UserCityQueriesInterface
{
    public function exists(UserCityDto $dto): bool
    {
        return UserCity::where('user_id', $dto->userId)
                ->where('city_id', $dto->cityId)
                ->exists();
    }
}

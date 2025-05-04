<?php

namespace App\Queries\Person\UsersCities;

use App\Models\Person\UserCity;

final class UserCityQueries implements UserCityQueriesInterface
{
    public function exists(int $userId, int $cityId): bool
    {
        return UserCity::where('user_id', $userId)
                ->where('city_id', $cityId)
                ->exists();
    }
}

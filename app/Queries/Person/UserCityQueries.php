<?php

namespace App\Queries\Person;

use App\Contracts\Database\DatabaseQueryInterface;
use App\Models\Person\UserCity;
use App\Services\Database\Person\Dto\UserCityDto;

class UserCityQueries implements DatabaseQueryInterface
{
    public function exists(UserCityDto $dto): bool
    {
        return UserCity::where('user_id', $dto->userId)
                ->where('city_id', $dto->cityId)
                ->exists();
    }
}

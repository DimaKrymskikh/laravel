<?php

namespace App\Modifiers\Person;

use App\Models\Person\UserCity;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\DatabaseQueryInterface;

class UserCityModifiers implements DatabaseQueryInterface
{
    public function save(UserCityDto $dto): void
    {
        UserCity::insert([
            'user_id' => $dto->userId,
            'city_id' => $dto->cityId
        ]);
    }
    
    public function remove(UserCityDto $dto): void
    {
        UserCity::where('user_id', $dto->userId)
                ->where('city_id', $dto->cityId)
                ->delete();
    }
}

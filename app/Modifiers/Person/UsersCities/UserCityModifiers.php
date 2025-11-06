<?php

namespace App\Modifiers\Person\UsersCities;

use App\Models\Person\UserCity;
use App\Services\Database\Person\Dto\UserCityDto;

final class UserCityModifiers implements UserCityModifiersInterface
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

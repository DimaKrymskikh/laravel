<?php

namespace App\Repositories\Person;

use App\Models\Person\UserCity;

final class UserCityRepository implements UserCityRepositoryInterface
{
    public function exists(int $userId, int $cityId): bool
    {
        return UserCity::where('user_id', $userId)
                ->where('city_id', $cityId)
                ->exists();
    }
    
    public function save(UserCity $userCity, int $userId, int $cityId): void
    {
        $userCity->user_id = $userId;
        $userCity->city_id = $cityId;
        $userCity->save();
    }
    
    public function delete(int $userId, int $cityId): void
    {
        UserCity::where('user_id', $userId)
                ->where('city_id', $cityId)
                ->delete();
    }
}

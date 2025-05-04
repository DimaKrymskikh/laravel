<?php

namespace App\Modifiers\Person\UsersCities;

use App\Models\Person\UserCity;

interface UserCityModifiersInterface
{
    public function save(UserCity $userCity, int $userId, int $cityId): void;
    
    public function delete(int $userId, int $cityId): void;
}

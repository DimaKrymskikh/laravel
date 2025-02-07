<?php

namespace App\Repositories\Person;

use App\Models\Person\UserCity;

interface UserCityRepositoryInterface
{
    public function exists(int $userId, int $cityId): bool;
    
    public function save(UserCity $userCity, int $userId, int $cityId): void;
    
    public function delete(int $userId, int $cityId): void;
}

<?php

namespace App\Modifiers\Person\UsersCities;

use App\Services\Database\Person\Dto\UserCityDto;

interface UserCityModifiersInterface
{
    public function save(UserCityDto $dto): void;
    
    public function remove(UserCityDto $dto): void;
}

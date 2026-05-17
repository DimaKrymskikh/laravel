<?php

namespace App\Modifiers\Person\Users;

use App\Models\User;
use App\Modifiers\ModifiersInterface;
use App\Services\Database\Person\Dto\RegisterDto;

interface UserModifiersInterface extends ModifiersInterface
{
    /**
     * Создаёт запись в таблице person.users.
     * 
     * @param User $user
     * @param RegisterDto $dto
     * @return User
     */
    public function create(User $user, RegisterDto $dto): User;
}

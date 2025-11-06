<?php

namespace App\Modifiers\Person\Users;

use App\Models\User;
use App\Modifiers\ModifiersInterface;
use App\Services\Database\Person\Dto\RegisterDto;

interface UserModifiersInterface extends ModifiersInterface
{
    public function create(User $user, RegisterDto $dto): void;
}

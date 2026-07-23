<?php

namespace App\Modifiers\Person;

use App\Models\User;
use App\Modifiers\Modifiers;
use App\Services\Database\Person\Dto\RegisterDto;
use Illuminate\Support\Facades\Hash;

class UserModifiers extends Modifiers
{
    /**
     * Создаёт запись в таблице person.users.
     * 
     * @param User $user
     * @param RegisterDto $dto
     * @return User
     */
    public function create(User $user, RegisterDto $dto): User
    {
        return $user::create([
                    'login' => $dto->login,
                    'email' => $dto->email,
                    'password' => Hash::make($dto->password),
                ]);
    }
}

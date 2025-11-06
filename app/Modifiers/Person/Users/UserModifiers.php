<?php

namespace App\Modifiers\Person\Users;

use App\Models\User;
use App\Modifiers\Modifiers;
use App\Services\Database\Person\Dto\RegisterDto;
use Illuminate\Support\Facades\Hash;

final class UserModifiers extends Modifiers implements UserModifiersInterface
{
    public function create(User $user, RegisterDto $dto): void
    {
        $user::create([
            'login' => $dto->login,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);
    }
}

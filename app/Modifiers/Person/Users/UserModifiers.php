<?php

namespace App\Modifiers\Person\Users;

use App\Models\User;

final class UserModifiers implements UserModifiersInterface
{
    public function saveField(User $user, string $field, mixed $value): void
    {
        $user->$field = $value;
        $user->save();
    }
}

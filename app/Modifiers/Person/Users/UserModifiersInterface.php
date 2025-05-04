<?php

namespace App\Modifiers\Person\Users;

use App\Models\User;

interface UserModifiersInterface
{
    public function saveField(User $user, string $field, mixed $value): void;
}

<?php

namespace App\Services\Database\Person;

use App\Models\User;
use App\Modifiers\Person\Users\UserModifiersInterface;

final class UserService
{
    public function __construct(
            private UserModifiersInterface $userModifiers,
    ) {
    }
    
    public function assignAdmin(User $user): void
    {
        $this->userModifiers->saveField($user, 'is_admin', true);
    }
    
    public function depriveAdmin(User $user): void
    {
        $this->userModifiers->saveField($user, 'is_admin', false);
    }
}

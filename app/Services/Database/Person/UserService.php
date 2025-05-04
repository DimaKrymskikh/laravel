<?php

namespace App\Services\Database\Person;

use App\Modifiers\Person\Users\UserModifiersInterface;
use App\Queries\Person\Users\UserQueriesInterface;

final class UserService
{
    public function __construct(
            private UserModifiersInterface $userModifiers,
            private UserQueriesInterface $userQueries
    ) {
    }
    
    public function assignAdmin(int $userId): void
    {
        $user = $this->userQueries->getById($userId);
        $this->userModifiers->saveField($user, 'is_admin', true);
    }
    
    public function depriveAdmin(int $userId): void
    {
        $user = $this->userQueries->getById($userId);
        $this->userModifiers->saveField($user, 'is_admin', false);
    }
}

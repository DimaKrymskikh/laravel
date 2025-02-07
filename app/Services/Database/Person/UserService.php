<?php

namespace App\Services\Database\Person;

use App\Repositories\Person\UserRepositoryInterface;

final class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }
    
    public function assignAdmin(int $userId): void
    {
        $user = $this->userRepository->getById($userId);
        $this->userRepository->saveField($user, 'is_admin', true);
    }
    
    public function depriveAdmin(int $userId): void
    {
        $user = $this->userRepository->getById($userId);
        $this->userRepository->saveField($user, 'is_admin', false);
    }
}

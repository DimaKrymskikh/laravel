<?php

namespace App\Services\Database\Person;

use App\Models\User;
use App\Modifiers\Person\Users\UserModifiersInterface;
use App\Services\Database\Person\Dto\RegisterDto;

final class UserService
{
    public function __construct(
            private UserModifiersInterface $userModifiers,
    ) {
    }
    
    public function create(RegisterDto $dto): User
    {
        $user = new User();
        $this->userModifiers->create($user, $dto);
        
        return $user;
    }
    
    public function assignAdmin(User $user): User
    {
        $user->is_admin = true;
        $this->userModifiers->save($user);
        
        return $user;
    }
    
    public function depriveAdmin(User $user): User
    {
        $user->is_admin = false;
        $this->userModifiers->save($user);
        
        return $user;
    }
    
    public function remove(User $user): void
    {
        $this->userModifiers->remove($user);
    }
}

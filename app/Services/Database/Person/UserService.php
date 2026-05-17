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
    
    /**
     * Регистрирует пользователя.
     * 
     * @param RegisterDto $dto
     * @return User
     */
    public function create(RegisterDto $dto): User
    {
        $user = new User();
        
        return $this->userModifiers->create($user, $dto);
    }
    
    /**
     * Пользователь получает права админа.
     * 
     * @param User $user
     * @return User
     */
    public function assignAdmin(User $user): User
    {
        $user->is_admin = true;
        $this->userModifiers->save($user);
        
        return $user;
    }
    
    /**
     * Пользователь лишается прав админа.
     * 
     * @param User $user
     * @return User
     */
    public function depriveAdmin(User $user): User
    {
        $user->is_admin = false;
        $this->userModifiers->save($user);
        
        return $user;
    }
    
    /**
     * Удаляет аккаунт пользователя.
     * 
     * @param User $user
     * @return void
     */
    public function remove(User $user): void
    {
        // Если поле remember_token таблицы person.users заполнено,
        // то Laravel не удаляет запись в таблице person.users.
        $user->setRememberToken(null);
        $this->userModifiers->save($user);
        
        $this->userModifiers->remove($user);
    }
}

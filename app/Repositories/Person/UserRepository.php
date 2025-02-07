<?php

namespace App\Repositories\Person;

use App\Models\User;

final class UserRepository implements UserRepositoryInterface
{
    public function getById(int $id): User
    {
        return User::find($id);
    }
    
    public function saveField(User $user, string $field, mixed $value): void
    {
        $user->$field = $value;
        $user->save();
    }
}

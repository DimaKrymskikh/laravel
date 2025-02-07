<?php

namespace App\Repositories\Person;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getById(int $id): User;
    
    public function saveField(User $user, string $field, mixed $value): void;
}

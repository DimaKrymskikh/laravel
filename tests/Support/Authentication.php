<?php

namespace  Tests\Support;

use App\Models\User;

trait Authentication
{
    public function getUserBaseTestLogin(): User
    {
        $this->seed([
            \Database\Seeders\Person\BaseTestUserSeeder::class,
        ]);
        
        return User::where('login', 'BaseTestLogin')->first();
    }
}

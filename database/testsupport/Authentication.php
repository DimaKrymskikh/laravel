<?php

namespace  Database\Testsupport;

use App\Models\User;
use Illuminate\Validation\ValidationException;

trait Authentication
{
    /**
     * Возвращает пользователя.
     * Если пользователи не посеяны, бросается исключение.
     * 
     * @return User
     * @throws type
     */
    private function getUser(string $login): User
    {
        $user = User::where('login', $login)->first();
        
        if(!$user) {
            throw ValidationException::withMessages([
                'testmessage' => trans('test.authuser.notseed')
            ]);
        }
        
        return $user;
    }
    
    private function seedUsers(): void
    {
        $this->seed([
            \Database\Seeders\Tests\Person\UserSeeder::class,
        ]);
    }
}

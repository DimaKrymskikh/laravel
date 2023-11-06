<?php

namespace  Tests\Support;

use App\Models\User;
use Illuminate\Validation\ValidationException;

trait Authentication
{
    public function getUserBaseTestLogin(): User
    {
        $this->seed([
            \Database\Seeders\Person\BaseTestUserSeeder::class,
        ]);
        
        return User::where('login', 'BaseTestLogin')->first();
    }
    
    /**
     * Возвращает аутентифицированного пользователя.
     * Если пользователи не посеяны, бросается исключение.
     * 
     * @return User
     * @throws type
     */
    public function getAuthUser(): User
    {
        $user = User::where('login', 'AuthTestLogin')->first();
        
        if(!$user) {
            throw ValidationException::withMessages([
                'testmessage' => trans('test.authuser.notseed')
            ]);
        }
        
        return $user;
    }
}

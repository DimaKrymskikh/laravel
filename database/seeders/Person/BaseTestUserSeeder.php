<?php

namespace Database\Seeders\Person;

use Database\Copy\Person\BaseTestUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseTestUserSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = (new BaseTestUser)();
        
        DB::table('person.users')->insert([
            'login' => $user->login,
            'password' => $user->password,
            'email' => $user->email,
        ]);
    }
}

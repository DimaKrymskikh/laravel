<?php

namespace Database\Seeders\Tests\Person;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    const ID_ADMIN_TEST_LOGIN = 1;
    const ID_AUTH_TEST_LOGIN = 2;
    const ID_TEST_LOGIN = 3;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'person.users';
        
        foreach ($this->getUsers() as $user) {
            DB::table($tableName)->insert([
                'id' => $user->id,
                'login' => $user->login,
                'password' => $user->password,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getUsers(): array
    {
        return [
            (object) [
                'id' => self::ID_ADMIN_TEST_LOGIN,
                'login' => 'AdminTestLogin',
                'password' => '$2y$10$/s60l1kRf7j3kQ/qp6TS1.IsFaZ6Lnn4rn3GnI8W1vo89TCqVvug.', // AdminTestPassword1
                'email' => 'admintestlogin@example.com',
                'is_admin' => true,
            ],
            (object) [
                'id' => self::ID_AUTH_TEST_LOGIN,
                'login' => 'AuthTestLogin',
                'password' => '$2y$10$Sna8cIKAgkNDOMXvfj5xYeFDY/e9lHieUseILF/N03QdLvqvw.xlC', // AuthTestPassword2
                'email' => 'authtestlogin@example.com',
                'is_admin' => false,
            ],
            (object) [
                'id' => self::ID_TEST_LOGIN,
                'login' => 'TestLogin',
                'password' => '$2y$10$C1KS0X3nGPMyirf4AXh9t.pzeEulJfgr.CWsKNsFAsQ7bDmC2oVOS', // TestPassword7
                'email' => 'testlogin@example.com',
                'is_admin' => false,
            ],
        ];
    }
}

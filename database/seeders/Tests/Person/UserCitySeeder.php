<?php

namespace Database\Seeders\Tests\Person;

use Database\Seeders\Tests\Thesaurus\CitySeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCitySeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'person.users_cities';
        
        foreach ($this->getUsersCities() as $userCity) {
            DB::table($tableName)->insert([
                'user_id' => $userCity->user_id,
                'city_id' => $userCity->city_id,
            ]);
        }
    }
    
    private function getUsersCities(): array
    {
        return [
            (object) [
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'city_id' => CitySeeder::ID_NOVOSIBIRSK,
            ],
            (object) [
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'city_id' => CitySeeder::ID_MOSCOW,
            ],
            (object) [
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'city_id' => CitySeeder::ID_TOMSK,
            ],
            (object) [
                'user_id' => UserSeeder::ID_TEST_LOGIN,
                'city_id' => CitySeeder::ID_NOVOSIBIRSK,
            ],
            (object) [
                'user_id' => UserSeeder::ID_TEST_LOGIN,
                'city_id' => CitySeeder::ID_OMSK,
            ],
        ];
    }
}

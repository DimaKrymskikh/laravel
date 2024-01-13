<?php

namespace Database\Seeders\Tests\Person;

use Database\Seeders\Tests\Dvd\FilmSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFilmSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'person.users_films';
        
        foreach ($this->getUsersFilms() as $userFilm) {
            DB::table($tableName)->insert([
                'user_id' => $userFilm->user_id,
                'film_id' => $userFilm->film_id,
            ]);
        }
    }
    
    private function getUsersFilms(): array
    {
        return [
            (object) [
                'user_id' => UserSeeder::ID_ADMIN_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_JAPANESE_RUN,
            ],
            (object) [
                'user_id' => UserSeeder::ID_ADMIN_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_NOVOCAINE_FLIGHT,
            ],
            (object) [
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_BOILED_DARES,
            ],
            (object) [
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_KISS_GLORY,
            ],
            (object) [
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_SHINING_ROSES,
            ],
            (object) [
                'user_id' => UserSeeder::ID_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_BOILED_DARES,
            ],
            (object) [
                'user_id' => UserSeeder::ID_TEST_LOGIN,
                'film_id' => FilmSeeder::ID_NOVOCAINE_FLIGHT,
            ],
        ];
    }
}

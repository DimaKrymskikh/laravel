<?php

namespace Tests\Feature\Commands\Person;

use App\Models\User;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FilmsStatisticsAvailableToUsersTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_statistics_films_and_users_in_console(): void
    {
        list($userBaseTestLogin, $userTestLogin, $userTestLogin2) = $this->getData();
        
        $this
            ->artisan('statistics:filmsAndUsers')
            ->expectsOutput("$userBaseTestLogin->login [$userBaseTestLogin->id] имеет в своём списке 3 фильмов.")
            ->expectsOutput("$userTestLogin->login [$userTestLogin->id] имеет в своём списке 2 фильмов.")
            ->expectsOutput("$userTestLogin2->login [$userTestLogin2->id] имеет в своём списке 0 фильмов.")
            ->assertExitCode(0);
    }
    
    
    public function test_statistics_films_and_users_in_file(): void
    {
        Storage::fake('local');
        
        $this->getData();
        
        $this
            ->artisan('statistics:filmsAndUsers --isFile')
            ->assertExitCode(0);
    }
    
    private function getData(): array
    {
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
            \Database\Seeders\Person\BaseTestUserSeeder::class,
        ]);
        $userBaseTestLogin = User::where('login', 'BaseTestLogin')->first();
        
        $userTestLogin = User::factory()->create();
        
        $userTestLogin2 = User::factory()
            ->state([
                'login' => 'UserTestLogin2',
                'email' => 'usertestlogin2@example.com'
            ])
            ->create();
        
        $films = Film::whereIn('id', [7, 15, 21, 555])->get();

        UserFilm::factory()
                ->count(5)
                ->state(new Sequence(
                    [
                        'user_id' => $userBaseTestLogin->id,
                        'film_id' => $films->get('id', 7)
                    ], [
                        'user_id' => $userBaseTestLogin->id,
                        'film_id' => $films->get('id', 15)
                    ], [
                        'user_id' => $userBaseTestLogin->id,
                        'film_id' => $films->get('id', 21)
                    ], [
                        'user_id' => $userTestLogin->id,
                        'film_id' => $films->get('id', 7)
                    ], [
                        'user_id' => $userTestLogin->id,
                        'film_id' => $films->get('id', 555)
                    ]
                ))
                ->create();
        
        return [$userBaseTestLogin, $userTestLogin, $userTestLogin2];
    }
}

<?php

namespace Tests\Feature\Commands\Dvd;

use App\Models\User;
use App\Models\Person\UserFilm;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmsStatisticsAvailableToUsersTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_statistics_films_and_users_in_console(): void
    {
        list($userTestLogin, $userTestLogin2) = $this->getData();
        
        $this
            ->artisan('statistics:filmsAndUsers')
            ->expectsOutput("BaseTestLogin [1] имеет в своём списке 3 фильмов.")
            ->expectsOutput("$userTestLogin->login [$userTestLogin->id] имеет в своём списке 2 фильмов.")
            ->expectsOutput("UserTestLogin2 [$userTestLogin2->id] имеет в своём списке 0 фильмов.")
            ->assertExitCode(0);
    }
    
    
    public function test_statistics_films_and_users_in_file(): void
    {
        $this->getData();
        
        $this
            ->artisan('statistics:filmsAndUsers true')
            ->assertExitCode(0);
    }
    private function getData(): array
    {
        $userTestLogin = User::factory()->create();
        
        $userTestLogin2 = User::factory()
            ->state([
                'login' => 'UserTestLogin2',
                'email' => 'usertestlogin2@example.com'
            ])
            ->create();

        UserFilm::factory()
                ->count(5)
                ->state(new Sequence(
                    ['film_id' => 7],
                    ['film_id' => 15],
                    ['film_id' => 21],
                    [
                        'user_id' => $userTestLogin->id,
                        'film_id' => 7
                    ],
                    [
                        'user_id' => $userTestLogin->id,
                        'film_id' => 555
                    ]
                ))
                ->create();
        
        return [$userTestLogin, $userTestLogin2];
    }
}

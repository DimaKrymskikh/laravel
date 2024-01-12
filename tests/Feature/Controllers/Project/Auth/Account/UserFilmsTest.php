<?php

namespace Tests\Feature\Controllers\Project\Auth\Account;

use App\Models\User;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use App\Notifications\Dvdrental\AddFilmNotification;
use App\Notifications\Dvdrental\RemoveFilmNotification;
use App\Providers\RouteServiceProvider;
use Database\Seeders\Tests\Person\UserSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Support\Authentication;
use Tests\TestCase;

class UserFilmsTest extends TestCase
{
    use RefreshDatabase, Authentication;
    
    public function test_user_films_displayed_for_auth(): void
    {
        $this->seedUsers();
        $userAuthTestLogin = $this->getUser('AuthTestLogin');
        
        $response = $this->before($userAuthTestLogin)->get(RouteServiceProvider::URL_AUTH_USERFILMS);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account/UserFilms')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                            // Отображается 3 фильма
                            $page->has('data', 3)
                                ->etc()
                )
            );
    }
    
    public function test_user_films_can_not_displayed_for_guest(): void
    {
        $response = $this->get(RouteServiceProvider::URL_AUTH_USERFILMS);

        $response
            ->assertStatus(302)
            ->assertRedirect('login');
    }
    
    public function test_user_films_displayed_for_auth_with_filter(): void
    {
        $this->seedUsers();
        $userAuthTestLogin = $this->getUser('AuthTestLogin');
        
        $response = $this->before($userAuthTestLogin)->get(RouteServiceProvider::URL_AUTH_USERFILMS.'?page=1&number=10&title_filter=center&description_filter=drama');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account/UserFilms')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                            // Отображается один фильм
                            $page->has('data', 1)
                                ->etc()
                )
            );
    }
    
    public function test_film_add_in_user_list(): void
    {
        Notification::fake();
        
        $this->seedUsers();
        $userAuthTestLogin = $this->getUser('AuthTestLogin');
        
        $response = $this->before($userAuthTestLogin)->post(RouteServiceProvider::URL_AUTH_USERFILMS.'/addfilm/123', [
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin теперь 4 фидьма (было 3 и 1 добавился)
        $this->assertEquals(4, UserFilm::where('user_id', $userAuthTestLogin->id)->count());
        
        // Отправляется оповещение о добавлении фильма
        Notification::assertSentTo(
            [$userAuthTestLogin], AddFilmNotification::class
        );
        
        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_AUTH_FILMS.'?page=1&number=100');
    }
    
    public function test_film_can_not_add_in_user_list_with_duplicate(): void
    {
        Notification::fake();
        
        $this->seedUsers();
        $userAuthTestLogin = $this->getUser('AuthTestLogin');
        
        // Попытка добавить в список пользователя уже существующий там фильм
        $response = $this->before($userAuthTestLogin)->post(RouteServiceProvider::URL_AUTH_USERFILMS.'/addfilm/7', [
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', $userAuthTestLogin->id)->count());

        // Оповещение о добавлении фильма не отправляется
        Notification::assertNotSentTo(
            [$userAuthTestLogin], AddFilmNotification::class
        );

        $response->assertInvalid([
                'message' => trans("user.film.message", [
                    'film' => Film::find(7)->title
                ])
            ]);
    }
    
    public function test_film_remove_from_user_list(): void
    {
        Notification::fake();
        
        $this->seedUsers();
        $userAuthTestLogin = $this->getUser('AuthTestLogin');
        
        $response = $this->before($userAuthTestLogin)->delete(RouteServiceProvider::URL_AUTH_USERFILMS.'/removefilm/7', [
            'password' => 'AuthTestPassword2',
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin теперь 2 фильма (было 3 и 1 удалился)
        $this->assertEquals(2, UserFilm::where('user_id', $userAuthTestLogin->id)->count());

        // Отправляется оповещение об удалении фильма
        Notification::assertSentTo(
            [$userAuthTestLogin], RemoveFilmNotification::class
        );

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_AUTH_USERFILMS.'?page=1&number=100');
    }
    
    public function test_film_can_not_remove_from_user_list_with_wrong_password(): void
    {
        Notification::fake();
        
        $this->seedUsers();
        $userAuthTestLogin = $this->getUser('AuthTestLogin');
        
        $response = $this->before($userAuthTestLogin)->delete(RouteServiceProvider::URL_AUTH_USERFILMS.'/removefilm/7', [
            'password' => 'wrongPassword7',
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', $userAuthTestLogin->id)->count());

        // Оповещение об удалении фильма не отправляется
        Notification::assertNotSentTo(
            [$userAuthTestLogin], RemoveFilmNotification::class
        );

        $response->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
    
    private function before(User $user): static
    {
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
        
        $films = Film::whereIn('id', [7, 15, 21])->get();

        // Создаём 4 записи в person.users_films
        // 3 для BaseTestLogin ($user)
        // 1 для TestLogin
        UserFilm::factory()->count(4)
                ->state(new Sequence(
                    [
                        'user_id' => $user->id,
                        'film_id' => $films->get('id', 7),
                    ], [
                        'user_id' => $user->id,
                        'film_id' => $films->get('id', 15),
                    ], [
                        'user_id' => $user->id,
                        'film_id' => $films->get('id', 21),
                    ], [
                        'user_id' => UserSeeder::ID_TEST_LOGIN,
                        'film_id' => $films->get('id', 7),
                    ]
                ))
                ->create();
    
        // Логинится пользователь BaseTestLogin
        return $this->actingAs($user);
    }
    
    public function test_filmcard_is_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\ActorSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
            \Database\Seeders\Dvd\FilmActorSeeder::class,
        ]);
        
        $response = $acting->get(RouteServiceProvider::URL_AUTH_USERFILMS.'/150');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/FilmCard')
                        ->has('errors', 0)
                        ->has('film', fn (Assert $page) => 
                            $page->where('id', 150)
                                ->has('actors', 5)
                                ->etc()
                )
            );
    }
    
    public function test_filmcard_can_not_displayed_for_guest(): void
    {
        $response = $this->get(RouteServiceProvider::URL_AUTH_USERFILMS.'/150');

        $response
            ->assertStatus(302)
            ->assertRedirect('login');
    }
}

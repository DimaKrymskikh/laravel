<?php

namespace Tests\Feature\Controllers\Project\Auth\Account;

use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use App\Notifications\Dvdrental\AddFilmNotification;
use App\Notifications\Dvdrental\RemoveFilmNotification;
use App\Providers\RouteServiceProvider;
use Database\Seeders\Tests\Dvd\FilmSeeder;
use Database\Seeders\Tests\Person\UserSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Database\Testsupport\Authentication;
use Database\Testsupport\Person\PersonData;
use Tests\TestCase;

class UserFilmsTest extends TestCase
{
    use RefreshDatabase, Authentication, PersonData;
    
    public function test_user_films_displayed_for_auth(): void
    {
        $this->seedUserFilms();
        $acting = $this->actingAs($this->getUser('AuthTestLogin'));
        
        $response = $acting->get(RouteServiceProvider::URL_AUTH_USERFILMS);

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
        $this->seedUserFilms();
        $acting = $this->actingAs($this->getUser('AuthTestLogin'));
        
        $response = $acting->get(RouteServiceProvider::URL_AUTH_USERFILMS.'?page=1&number=10&title_filter=boiled&description_filter=story&release_year_filter=2006&language_name_filter=Русский');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account/UserFilms')
            );
    }
    
    public function test_film_add_in_user_list(): void
    {
        Notification::fake();
        
        $this->seedUserFilms();
        $nFilms = UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->post(RouteServiceProvider::URL_AUTH_USERFILMS.'/addfilm/'.FilmSeeder::ID_RIVER_OUTLAW, [
            'page' => 1,
            'number' => 100
        ]);
        
        // В коллекции AuthTestLogin добавился один фильм
        $this->assertEquals($nFilms + 1, UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count());
        
        // Отправляется оповещение о добавлении фильма
        Notification::assertSentTo(
            [$user], AddFilmNotification::class
        );
        
        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_AUTH_FILMS.'?page=1&number=100&title_filter=&description_filter=&release_year_filter=&language_name_filter=');
    }
    
    public function test_film_can_not_add_in_user_list_with_duplicate(): void
    {
        Notification::fake();
        
        $this->seedUserFilms();
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        // Попытка добавить в список пользователя уже существующий там фильм
        $response = $acting->withHeaders([
            'X-Inertia' => true,
        ])->post(RouteServiceProvider::URL_AUTH_USERFILMS.'/addfilm/'.FilmSeeder::ID_BOILED_DARES, [
            'page' => 1,
            'number' => 100
        ]);
        
        // У AuthTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count());

        // Оповещение о добавлении фильма не отправляется
        Notification::assertNotSentTo(
            [$user], AddFilmNotification::class
        );

        $filmTitle = Film::find(FilmSeeder::ID_BOILED_DARES)->title;
        $response->assertInvalid([
                'message' => "Фильм '$filmTitle' уже находится в вашей коллекции."
            ]);
    }
    
    public function test_film_remove_from_user_list(): void
    {
        Notification::fake();
        
        $this->seedUserFilms();
        $nFilms = UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->delete(RouteServiceProvider::URL_AUTH_USERFILMS.'/removefilm/'.FilmSeeder::ID_BOILED_DARES, [
            'password' => 'AuthTestPassword2',
            'page' => 1,
            'number' => 100
        ]);
        
        // Из коллекции AuthTestLogin удалён один фильм
        $this->assertEquals($nFilms - 1, UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count());

        // Отправляется оповещение об удалении фильма
        Notification::assertSentTo(
            [$user], RemoveFilmNotification::class
        );

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_AUTH_USERFILMS.'?page=1&number=100&title_filter=&description_filter=&release_year_filter=&language_name_filter=');
    }
    
    public function test_film_can_not_remove_from_user_list_if_film_not_exists(): void
    {
        Notification::fake();
        
        $this->seedUserFilms();
        $nFilms = UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        // Передаётся id фильма, которого нет в коллекции пользователя AuthTestLogin
        $response = $acting->withHeaders([
            'X-Inertia' => true,
        ])->delete(RouteServiceProvider::URL_AUTH_USERFILMS.'/removefilm/'.FilmSeeder::ID_JAPANESE_RUN, [
            'password' => 'AuthTestPassword2',
            'page' => 1,
            'number' => 100
        ]);
        
        // Коллекция пользователя AuthTestLogin не изменилась
        $this->assertEquals($nFilms, UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count());

        // Оповещение об удалении фильма не отправляется
        Notification::assertNotSentTo(
            [$user], RemoveFilmNotification::class
        );

        $filmTitle = Film::find(FilmSeeder::ID_JAPANESE_RUN)->title;
        $response->assertInvalid([
                'message' => "Фильма '$filmTitle' нет в вашей коллекции. Удаление невозможно."
            ]);
    }
    
    public function test_film_can_not_remove_from_user_list_with_wrong_password(): void
    {
        Notification::fake();
        
        $this->seedUserFilms();
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->delete(RouteServiceProvider::URL_AUTH_USERFILMS.'/removefilm/'.FilmSeeder::ID_BOILED_DARES, [
            'password' => 'wrongPassword7',
            'page' => 1,
            'number' => 100
        ]);
        
        // У AuthTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', UserSeeder::ID_AUTH_TEST_LOGIN)->count());

        // Оповещение об удалении фильма не отправляется
        Notification::assertNotSentTo(
            [$user], RemoveFilmNotification::class
        );

        $response->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
    
    public function test_filmcard_is_displayed_for_auth(): void
    {
        $this->seedUserFilmsWithActors();
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->get(RouteServiceProvider::URL_AUTH_USERFILMS.'/'.FilmSeeder::ID_BOILED_DARES);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/FilmCard')
                        ->has('errors', 0)
                        ->has('film', fn (Assert $page) => 
                            $page->where('id', FilmSeeder::ID_BOILED_DARES)
                                ->has('actors', 2)
                                ->etc()
                )
            );
    }
    
    public function test_filmcard_can_not_displayed_for_guest(): void
    {
        $response = $this->get(RouteServiceProvider::URL_AUTH_USERFILMS.'/'.FilmSeeder::ID_BOILED_DARES);

        $response
            ->assertStatus(302)
            ->assertRedirect('login');
    }
}

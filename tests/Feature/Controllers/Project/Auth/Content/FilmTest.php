<?php

namespace Tests\Feature\Controllers\Project\Auth\Content;

use App\Models\User;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use App\Notifications\Dvdrental\AddFilmNotification;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase, Authentication;
        
    public function test_films_list_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        
        $this->seed([
            \Database\Seeders\Thesaurus\LanguageSeeder::class,
            \Database\Seeders\Dvd\FilmSeeder::class,
        ]);
        
        $response = $acting->get('films?number=10&title=ar&description=ep&page=2');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Films')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                                $page->where('current_page', 2)
                                    ->has('data', 9) // Число фильмов на странице
                                    ->where('from', 11)
                                    ->where('last_page', 2)
                                    ->has('links')
                                    ->where('per_page', 10)
                                    ->where('to', 19)
                                    ->where('total', 19)
                                    ->etc()
                            )
            );
    }
    
    public function test_film_add_in_user_list(): void
    {
        Notification::fake();
        
        // Получаем пользователя BaseTestLogin
        $userBaseTestLogin = $this->getUserBaseTestLogin();
        
        $response = $this->before($userBaseTestLogin)->post('films/addfilm/123', [
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin теперь 4 фидьма (было 3 и 1 добавился)
        $this->assertEquals(4, UserFilm::where('user_id', $userBaseTestLogin->id)->count());
        
        // Отправляется оповещение о добавлении фильма
        Notification::assertSentTo(
            [$userBaseTestLogin], AddFilmNotification::class
        );
        
        $response
            ->assertStatus(302)
            ->assertRedirect('films?page=1&number=100');
    }
    
    public function test_film_can_not_add_in_user_list_with_duplicate(): void
    {
        Notification::fake();
        
        // Получаем пользователя BaseTestLogin
        $userBaseTestLogin = $this->getUserBaseTestLogin();
        
        // Попытка добавить в список пользователя уже существующий там фильм
        $response = $this->before($userBaseTestLogin)->post('films/addfilm/7', [
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', $userBaseTestLogin->id)->count());

        // Оповещение о добавлении фильма не отправляется
        Notification::assertNotSentTo(
            [$userBaseTestLogin], AddFilmNotification::class
        );

        $response->assertInvalid([
                'message' => trans("user.film.message", [
                    'film' => Film::find(7)->title
                ])
            ]);
    }
    
    private function before(User $user): static
    {
        $userTestLogin = User::factory()->create();
        
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
                        'user_id' => $userTestLogin->id,
                        'film_id' => $films->get('id', 7),
                    ]
                ))
                ->create();
    
        // Логинится пользователь BaseTestLogin
        return $this->actingAs($user);
    }
}

<?php

namespace Tests\Feature\Dvdrental;

use App\Models\User;
use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_account_displayed_for_auth(): void
    {
        $response = $this->beforeTest()->get('account');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account')
                        ->has('errors', 0)
                        ->has('films', fn (Assert $page) => 
                            // Отображается 3 фильма
                            $page->has('data', 3)
                                ->etc()
                )
            );
    }
    
    public function test_account_can_not_displayed_for_guest(): void
    {
        $response = $this->get('account');

        $response
            ->assertStatus(302)
            ->assertRedirect('login');
    }
    
    public function test_account_displayed_for_auth_with_filter(): void
    {
        $response = $this->beforeTest()->get('account?page=1&number=10&title=center&description=drama');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account')
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
        $response = $this->beforeTest()->post('account/addfilm/123', [
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin теперь 4 фидьма (было 3 и 1 добавился)
        $this->assertEquals(4, UserFilm::where('user_id', 1)->count());

        $response
            ->assertStatus(302)
            ->assertRedirect('catalog?page=1&number=100&title=&description=');
    }
    
    public function test_film_add_in_user_list_with_duplicate(): void
    {
        // Попытка добавить в список пользователя уже существующий там фильм
        $response = $this->beforeTest()->post('account/addfilm/7', [
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', 1)->count());

        $response->assertInvalid([
                'message' => trans("user.film.message", [
                    'film' => Film::find(7)->title
                ])
            ]);
    }
    
    public function test_film_remove_from_user_list(): void
    {
        $response = $this->beforeTest()->delete('account/removefilm/7', [
            'password' => 'TestPassword7',
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin теперь 2 фидьма (было 3 и 1 удалился)
        $this->assertEquals(2, UserFilm::where('user_id', 1)->count());

        $response
            ->assertStatus(302)
            ->assertRedirect('account?page=1&number=100&title=&description=');
    }
    
    public function test_film_can_not_remove_from_user_list_with_wrong_password(): void
    {
        $response = $this->beforeTest()->delete('account/removefilm/7', [
            'password' => 'wrongPassword7',
            'page' => 1,
            'number' => 100
        ]);
        
        // У BaseTestLogin по-прежнему 3 фильма
        $this->assertEquals(3, UserFilm::where('user_id', 1)->count());

        $response->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
    
    private function beforeTest(): static
    {
        $userTestLogin = User::factory()->create();

        // Создаём 4 записи в person.users_films
        // 3 для BaseTestLogin
        // 1 для TestLogin
        UserFilm::factory()->count(4)
                ->state(new Sequence(
                    // Airplane Sierra [A Touching Saga of a Hunter And a Butler who must Discover a Butler in A Jet Boat]
                    ['film_id' => 7],
                    // Alien Center [A Brilliant Drama of a Cat And a Mad Scientist who must Battle a Feminist in A MySQL Convention]
                    ['film_id' => 15],
                    // American Circus [A Insightful Drama of a Girl And a Astronaut who must Face a Database Administrator in A Shark Tank]
                    ['film_id' => 21],
                    [
                        'user_id' => $userTestLogin->id,
                        'film_id' => 7
                    ]
                ))
                ->create();
    
        // Получаем пользователя BaseTestLogin
        $userBaseTestLogin = User::find(1);
        
        // Логинится пользователь BaseTestLogin
        return $this->actingAs($userBaseTestLogin);
    }
}

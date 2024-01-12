<?php

namespace Tests\Feature\Controllers\Project\Admin\Content;

use App\Models\Dvd\Film;
use App\Providers\RouteServiceProvider;
use Database\Seeders\Tests\Dvd\ActorSeeder;
use Database\Seeders\Tests\Dvd\FilmSeeder;
use Database\Seeders\Tests\Thesaurus\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\Support\Seeders;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase, Authentication, Seeders;
    
    public function test_films_list_page_displayed_for_admin_without_films(): void
    {
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_FILMS);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Films')
                        ->has('films', fn (Assert $page) => 
                            $page->has('data', 0)
                                ->etc()
                        )
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_films_list_page_displayed_for_admin_with_films(): void
    {
        $this->seedFilmsAndActors();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_FILMS);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Films')
                        ->has('films', fn (Assert $page) => 
                            $page->has('data', Film::all()->count())
                                ->etc()
                        )
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_films_list_page_can_not_displayed_for_auth_not_admin(): void
    {
        $this->seedFilmsAndActors();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AuthTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_FILMS);

        $response->assertStatus(403);
    }
    
    public function test_films_list_can_be_filtered(): void
    {
        $this->seedFilmsAndActors();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $title = 'Ap';
        $description = 'Oo';
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_FILMS."?title_filter=$title&description_filter=$description");

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Films')
                        ->has('films', fn (Assert $page) => 
                            $page->has('data', Film::where('title', 'ILIKE', "%$title%")
                                        ->where('description', 'ILIKE', "%$description%")
                                        ->count()
                            )
                            ->etc()
                        )
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_admin_can_add_film(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_FILMS, [
            'title' => 'Название фильма',
            'description' => 'Описание фильма',
            'release_year' => '2020',
            'language_id' => LanguageSeeder::ID_RUSSIAN,
        ]);

        // Добавлен новый фильм в таблицу 'dvd.films'
        $this->assertEquals($nFilms + 1, Film::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20&title_filter=&description_filter=&release_year_filter=');
    }
    
    public function test_admin_can_add_film_with_nullable_data(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_FILMS, [
            'title' => 'Название фильма',
            'description' => '',
            'release_year' => null,
            'language_id' => LanguageSeeder::ID_RUSSIAN,
        ]);

        // Добавлен новый фильм в таблицу 'dvd.films'
        $this->assertEquals($nFilms + 1, Film::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20&title_filter=&description_filter=&release_year_filter=');
    }
    
    public function test_admin_can_not_add_if_the_release_year_is_not_an_integer(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_FILMS, [
            'title' => 'Название фильма',
            'description' => 'Описание фильма',
            'release_year' => 'aa',
            'language_id' => LanguageSeeder::ID_RUSSIAN,
        ]);

        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertInvalid([
                'release_year' => trans('attr.film.release_year.integer'),
            ]);
    }
    
    public function test_admin_can_update_film_title(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        
        $newTitle = 'Новое название фильма';
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'title',
            'title' => $newTitle,
        ]);

        // Изменилось название фильма
        $this->assertEquals($newTitle, Film::find($filmId)->title);
        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20');
    }
    
    public function test_admin_can_not_update_film_title_if_the_title_is_empty(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'title',
            'title' => '',
        ]);

        // Название фильма не изменилось
        $this->assertEquals('Adaptation Holes', Film::find($filmId)->title);
        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertInvalid([
                'title' => trans('attr.film.title.required'),
            ]);
    }
    
    public function test_admin_can_update_film_description(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        
        $newDescription = 'Новое описание фильма';
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'description',
            'description' => $newDescription,
        ]);

        $this->assertEquals($newDescription, Film::find($filmId)->description);
        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20');
    }
    
    public function test_admin_can_update_film_language(): void
    {
        $this->seedFilmsAndActors();
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $this->assertEquals(LanguageSeeder::ID_ENGLISH, Film::find($filmId)->language_id);
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        
        $newLanguageId = LanguageSeeder::ID_RUSSIAN;
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'language_id',
            'language_id' => $newLanguageId,
        ]);

        $this->assertEquals($newLanguageId, Film::find($filmId)->language_id);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20');
    }
    
    public function test_admin_can_reset_to_null_the_film_language(): void
    {
        $this->seedFilmsAndActors();
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $this->assertEquals(LanguageSeeder::ID_ENGLISH, Film::find($filmId)->language_id);
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'language_id',
            'language_id' => null,
        ]);

        $this->assertNull(Film::find($filmId)->language_id);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20');
    }
    
    public function test_admin_can_update_film_release_year(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        
        $newReleaseYear = '2023';
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'release_year',
            'release_year' => $newReleaseYear,
        ]);

        $this->assertEquals($newReleaseYear, Film::find($filmId)->release_year);
        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20');
    }
    
    public function test_admin_can_not_update_film_release_year_if_the_release_year_is_not_an_integer(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        
        $newReleaseYear = 'xxx';
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'field' => 'release_year',
            'release_year' => $newReleaseYear,
        ]);

        $this->assertNotEquals($newReleaseYear, Film::find($filmId)->release_year);
        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertInvalid([
                'release_year' => trans('attr.film.release_year.integer'),
            ]);
    }
    
    public function test_admin_can_delete_film(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'password' => 'AdminTestPassword1',
        ]);

        // В таблице 'dvd.films' число фильмов уменьшилось на 1
        $this->assertEquals($nFilms - 1, Film::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_FILMS.'?page=1&number=20');
    }
    
    public function test_admin_can_not_delete_film_if_the_password_is_incorrect(): void
    {
        $this->seedFilmsAndActors();
        $nFilms = Film::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_FILMS."/$filmId", [
            'password' => 'IncorrectPassword13',
        ]);

        // В таблице 'dvd.films' число фильмов не изменилось
        $this->assertEquals($nFilms, Film::all()->count());

        $response
            ->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
    
    public function test_admin_can_get_actors_list_of_the_film(): void
    {
        $this->seedFilmsAndActors();
        $this->seedUsers();
        
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $filmId = FilmSeeder::ID_ADAPTATION_HOLES;
        $response = $acting->getJson(RouteServiceProvider::URL_ADMIN_FILMS."/getActorsList/$filmId");

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('id', $filmId)
                    ->has('actors', 3)
                    ->has('actors.0', fn (AssertableJson $json) =>
                            $json->where('id', ActorSeeder::ID_ED_CHASE)
                                ->where('first_name', 'Ed')
                                ->where('last_name', 'Chase')
                                ->etc()
                        )
                    ->has('actors.1', fn (AssertableJson $json) =>
                            $json->where('id', ActorSeeder::ID_JENNIFER_DAVIS)
                                ->where('first_name', 'Jennifer')
                                ->where('last_name', 'Davis')
                                ->etc()
                        )
                    ->has('actors.2', fn (AssertableJson $json) =>
                            $json->where('id', ActorSeeder::ID_JOHNNY_LOLLOBRIGIDA)
                                ->where('first_name', 'Johnny')
                                ->where('last_name', 'Lollobrigida')
                                ->etc()
                        )
                ->etc()
            );
    }
}

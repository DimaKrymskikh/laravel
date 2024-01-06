<?php

namespace Tests\Feature\Controllers\Project\Admin\Content;

use App\Models\Dvd\Actor;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use Database\Seeders\Tests\Dvd\ActorSeeder;
use Database\Seeders\Tests\Dvd\FilmSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Support\Authentication;
use Tests\Support\Seeders;
use Tests\TestCase;

class FilmActorTest extends TestCase
{
    use RefreshDatabase, Authentication, Seeders;
    
    public function test_admin_can_get_actors_list(): void
    {
        $this->seedFilmsAndActors();
        $this->seedUsers();
        
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->getJson("admin/films/actors?name=nn&film_id=".FilmSeeder::ID_JAPANESE_RUN);

        // Фильтру name=nn отвечают два актёра ActorSeeder::ID_JENNIFER_DAVIS и ActorSeeder::ID_JOHNNY_LOLLOBRIGIDA
        // У фильма FilmSeeder::ID_JAPANESE_RUN два актёра ActorSeeder::ID_JOHNNY_LOLLOBRIGIDA и ActorSeeder::ID_NICK_WAHLBERG
        // Возвращается только один ActorSeeder::ID_JENNIFER_DAVIS
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has(1)
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('first_name', 'Jennifer')
                            ->where('last_name', 'Davis')
                            ->has('id')
                            ->etc()
                    )
            );
    }
    
    public function test_admin_can_add_the_actor_in_the_film(): void
    {
        $this->seedFilmsAndActors();
        $nFilmsActors = FilmActor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post('admin/films/actors', [
            'film_id' => FilmSeeder::ID_ADAPTATION_HOLES,
            'actor_id' => ActorSeeder::ID_PENELOPE_GUINESS,
        ]);

        $this->assertEquals($nFilmsActors + 1, FilmActor::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect('admin/films');
    }
    
    public function test_admin_can_not_add_the_actor_in_the_film_if_the_couple_exists(): void
    {
        $this->seedFilmsAndActors();
        $nFilmsActors = FilmActor::all()->count();
        
        $this->seedUsers();
        $film = Film::find(FilmSeeder::ID_JAPANESE_RUN);
        $actor = Actor::find(ActorSeeder::ID_NICK_WAHLBERG);
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post('admin/films/actors', [
            'film_id' => $film->id,
            'actor_id' => $actor->id,
        ]);

        $this->assertEquals($nFilmsActors, FilmActor::all()->count());

        $response
            ->assertInvalid([
                'message' => trans("attr.film.contains.actor", [
                    'film' => $film->title,
                    'actor' => "$actor->first_name $actor->last_name"
                ])
            ]);
    }
    
    public function test_admin_can_delete_the_actor_from_the_film(): void
    {
        $this->seedFilmsAndActors();
        $nFilmsActors = FilmActor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete('admin/films/actors/'.ActorSeeder::ID_PENELOPE_GUINESS, [
            'password' => 'AdminTestPassword1',
            'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
        ]);

        // В таблице 'dvd.actors' число актёров уменьшилось на 1
        $this->assertEquals($nFilmsActors - 1, FilmActor::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect('admin/films');
    }
    
    public function test_admin_can_not_delete_the_actor_from_the_film_if_the_password_is_incorrect(): void
    {
        $this->seedFilmsAndActors();
        $nFilmsActors = FilmActor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete('admin/films/actors/'.ActorSeeder::ID_PENELOPE_GUINESS, [
            'password' => 'IncorrectPassword13',
            'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
        ]);

        $this->assertEquals($nFilmsActors, FilmActor::all()->count());

        $response
            ->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
}

<?php

namespace Tests\Feature\Controllers\Project\Admin\Content;

use App\Models\Dvd\Actor;
use App\Providers\RouteServiceProvider;
use App\Queries\Dvd\Actors\ActorQueriesInterface;
use Database\Seeders\Tests\Dvd\ActorSeeder;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\Support\Seeders;
use Tests\TestCase;

class ActorTest extends TestCase
{
    use RefreshDatabase, Authentication, Seeders;
    
    public function test_actors_page_displayed_for_admin_without_actors(): void
    {
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_ACTORS);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Actors')
                        ->has('actors', fn (Assert $page) => 
                            $page->has('data', 0)
                                ->etc()
                        )
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_actors_page_displayed_for_admin_with_actors(): void
    {
        $this->seedActors();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_ACTORS);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Actors')
                        ->has('actors', fn (Assert $page) => 
                            $page->has('data', Actor::all()->count())
                                ->etc()
                        )
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_actors_page_can_not_displayed_for_auth_not_admin(): void
    {
        $this->seedActors();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AuthTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_ACTORS);

        $response->assertStatus(403);
    }
    
    public function test_actors_list_can_be_filtered(): void
    {
        $this->seedActors();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_ACTORS.'?name=er');

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Actors')
                        ->has('actors', fn (Assert $page) => 
                            $page->has('data', Actor::where(function (Builder $query) {
                                        $query->selectRaw("concat(first_name, ' ', last_name)")
                                            ->from('dvd.actors as a')
                                            ->whereColumn('a.id', 'dvd.actors.id');
                                    }, 'ILIKE', "%er%")->count())
                                ->etc()
                        )
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_admin_can_add_actor(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_ACTORS, [
            'first_name' => 'Андрей',
            'last_name' => 'Миронов',
        ]);

        // Добавлен новый актёр в таблицу 'dvd.actors'
        $this->assertEquals($nActors + 1, Actor::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_ACTORS.'?page=1&number=20&name=');
    }
    
    public function test_admin_can_not_add_if_the_first_name_or_the_last_name_starts_with_a_small_letter(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_ACTORS, [
            'first_name' => 'андрей',
            'last_name' => 'миронов',
        ]);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());

        $response
            ->assertInvalid([
                'first_name' => trans('attr.actor.first_name.capital_first_letter'),
                'last_name' => trans('attr.actor.last_name.capital_first_letter'),
            ]);
    }
    
    public function test_admin_can_not_add_if_the_first_name_or_the_last_name_is_empty(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_ACTORS, [
            'first_name' => '',
            'last_name' => 'Миронов',
        ]);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());

        $response
            ->assertInvalid([
                'first_name' => trans('attr.actor.first_name.required'),
            ]);
    }
    
    public function test_admin_can_not_add_if_the_first_name_or_the_last_name_is_not_a_string(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_ACTORS, [
            'first_name' => 'Андрей',
            'last_name' => 77,
        ]);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());

        $response
            ->assertInvalid([
                'last_name' => trans('attr.actor.last_name.string')
            ]);
    }
    
    public function test_admin_can_update_actor(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_ACTORS.'/'.ActorSeeder::ID_JENNIFER_DAVIS, [
            'first_name' => 'Андрей',
            'last_name' => 'Миронов',
        ]);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());
        // Изменилось имя актёра
        $this->assertEquals('Андрей', Actor::find(ActorSeeder::ID_JENNIFER_DAVIS)->first_name);
        // Изменилась фамилия актёра
        $this->assertEquals('Миронов', Actor::find(ActorSeeder::ID_JENNIFER_DAVIS)->last_name);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_ACTORS.'?page=1&number=20&name=');
    }
    
    public function test_admin_can_not_update_actor_if_the_actor_is_not_in_the_table(): void
    {
        $this->seedActors();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->withHeaders([
            'X-Inertia' => true,
        ])->put(RouteServiceProvider::URL_ADMIN_ACTORS.'/'.ActorSeeder::ID_NOT, [
            'first_name' => 'Андрей',
            'last_name' => 'Миронов',
        ]);

        $response
            ->assertInvalid([
                'message' => sprintf(ActorQueriesInterface::NOT_RECORD_WITH_ID, ActorSeeder::ID_NOT),
            ])
            ->assertStatus(303);
    }
    
    public function test_admin_can_not_update_actor_if_the_first_name_or_the_last_name_starts_with_a_small_letter(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_ACTORS.'/'.ActorSeeder::ID_JENNIFER_DAVIS, [
            'first_name' => 'Андрей',
            'last_name' => 'миронов',
        ]);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());
        // Имя актёра не изменилось
        $this->assertEquals('Jennifer', Actor::find(ActorSeeder::ID_JENNIFER_DAVIS)->first_name);
        // Фамилия актёра не изменилась
        $this->assertEquals('Davis', Actor::find(ActorSeeder::ID_JENNIFER_DAVIS)->last_name);

        $response
            ->assertInvalid([
                'last_name' => trans('attr.actor.last_name.capital_first_letter'),
            ]);
    }
    
    public function test_admin_can_delete_actor(): void
    {
        $this->seedFilmsAndActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_ACTORS.'/'.ActorSeeder::ID_JENNIFER_DAVIS, [
            'password' => 'AdminTestPassword1',
        ]);

        // В таблице 'dvd.actors' число актёров уменьшилось на 1
        $this->assertEquals($nActors - 1, Actor::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_ACTORS.'?page=1&number=20&name=');
    }
    
    public function test_admin_can_not_delete_actor_if_the_password_is_incorrect(): void
    {
        $this->seedFilmsAndActors();
        $nActors = Actor::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_ACTORS.'/'.ActorSeeder::ID_JENNIFER_DAVIS, [
            'password' => 'IncorrectPassword13',
        ]);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());

        $response
            ->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
}

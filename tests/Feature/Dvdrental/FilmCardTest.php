<?php

namespace Tests\Feature\Dvdrental;

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmCardTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_filmcard_is_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        
        $response = $acting->get('filmcard/150');

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
        $response = $this->get('filmcard/150');

        $response
            ->assertStatus(302)
            ->assertRedirect('login');
    }
}

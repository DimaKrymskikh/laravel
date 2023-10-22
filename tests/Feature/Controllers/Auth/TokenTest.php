<?php

namespace Tests\Feature\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\TestCase;

class TokenTest extends TestCase
{
    use RefreshDatabase, Authentication;
    
    public function test_user_redirects_after_ctrl_F5(): void
    {
        $acting = $this->actingAs($this->getUserBaseTestLogin());
        $response = $acting->get('token');

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::HOME);
    }
    
    public function test_token_can_be_gotten_for_auth(): void
    {
        $acting = $this->actingAs($this->getUserBaseTestLogin());
        $response = $acting->post('token');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Token')
                        ->has('errors', 0)
                        ->has('token')
                        ->has('user')
                        ->etc()
            );
    }
}

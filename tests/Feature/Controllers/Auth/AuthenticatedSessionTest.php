<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_login_form_is_displayed_for_quest(): void
    {
        $response = $this->get('login');
        $this->assertGuest();
        
        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Login')
                        ->has('errors', 0)
            );
    }

    public function test_login_form_can_not_displayed_for_auth(): void
    {
        $response = $this->authUser()->get('login');
        
        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::HOME);
        
        $this->assertAuthenticated();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $this->assertGuest();

        $response = $this->post('login', [
            'login' => $user->login,
            'password' => 'TestPassword7',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('login', [
            'login' => $user->login,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'password' => trans("auth.failedlogin")
            ]);
    }
    
    public function test_users_can_logout_if_auth(): void
    {
        $response = $this->authUser()->get('logout');
        $this->assertGuest();
        
        $response->assertRedirect('guest');
    }
    
    private function authUser(): static 
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        $this->assertAuthenticated();
        
        return $acting;
    }
}

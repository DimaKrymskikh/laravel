<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_admin_site_displayed_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $response = $acting->get('admin');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Home')
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_admin_site_not_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        $response = $acting->get('admin');

        $response
            ->assertStatus(403);
    }

    public function test_auth_user_can_become_admin(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        $this->assertAuthenticated();
        
        $response = $acting->post('admin/create', [
            'password' => 'TestPassword7',
        ]);
        
        $this->assertTrue((User::where('id', $user->id)->first())->is_admin);

        $response
            ->assertStatus(302)
            ->assertRedirect('/account');
    }

    public function test_admin_can_revoke_admin_rights(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $acting = $this->actingAs($user);
        $this->assertAuthenticated();
        
        $response = $acting->post('admin/destroy', [
            'password' => 'TestPassword7',
        ]);
        
        $this->assertFalse((User::where('id', $user->id)->first())->is_admin);

        $response
            ->assertStatus(302)
            ->assertRedirect('/account');
    }
}

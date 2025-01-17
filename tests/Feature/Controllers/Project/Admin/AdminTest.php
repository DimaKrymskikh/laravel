<?php

namespace Tests\Feature\Controllers\Project\Admin;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

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
            ->assertRedirect(RouteServiceProvider::URL_AUTH_USERFILMS.'?page=1&number=20&title_filter=&description_filter=&release_year_filter=');
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
            ->assertRedirect(RouteServiceProvider::URL_AUTH_USERFILMS.'?page=1&number=20&title_filter=&description_filter=&release_year_filter=');
    }
}

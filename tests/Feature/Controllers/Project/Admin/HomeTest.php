<?php

namespace Tests\Feature\Controllers\Project\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomeTest extends TestCase
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
}

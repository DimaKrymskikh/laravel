<?php

namespace Tests\Feature\Controllers\Project\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_homepage_displayed_for_auth(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);

        $response = $acting->get('/');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Home')
                        ->has('errors', 0)
            );
    }
}

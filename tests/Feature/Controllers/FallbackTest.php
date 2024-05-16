<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\TestCase;

class FallbackTest extends TestCase
{
    use RefreshDatabase, Authentication;
    
    public function test_fallback_for_guest(): void
    {
        $response = $this->get('fail');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Fallback')
                        ->has('errors', 0)
            );
    }
    
    public function test_fallback_for_auth(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->get('fail');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Fallback')
                        ->has('errors', 0)
            );
    }
    
    public function test_fallback_for_admin(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        $acting = $this->actingAs($user);
        $response = $acting->get('fail');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Fallback')
                        ->has('errors', 0)
            );
    }
}

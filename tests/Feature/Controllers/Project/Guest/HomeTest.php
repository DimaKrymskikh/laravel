<?php

namespace Tests\Feature\Controllers\Project\Guest;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_homepage_displayed_for_guest(): void
    {
        $response = $this->get('guest');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Home')
                        ->has('errors', 0)
            );
    }
}

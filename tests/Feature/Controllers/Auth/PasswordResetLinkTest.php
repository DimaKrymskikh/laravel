<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_password_reset_screen_can_be_rendered(): void
    {
        $response = $this->get('forgot-password');
        $this->assertGuest();

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/ForgotPassword')
                        ->has('errors', 0)
                        ->has('status', null)
            );
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_password_link_can_not_be_requested_with_non_existent_mail(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('forgot-password', ['email' => 'non-existent@mail.com']);

        Notification::assertNotSentTo($user, ResetPasswordNotification::class);

        $response->assertInvalid([
                'email' => trans("passwords.user")
            ]);
    }
}

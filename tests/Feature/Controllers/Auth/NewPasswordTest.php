<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        
        $response = $this->get('/reset-password/'.Str::random(60), ['email' => $user->email]);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/ResetPassword')
                        ->has('errors', 0)
            );
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'NewPassword8',
                'password_confirmation' => 'NewPassword8',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }

    public function test_password_can_not_be_reset_if_token_replace(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => 'replaced_token',
                'email' => $user->email,
                'password' => 'NewPassword8',
                'password_confirmation' => 'NewPassword8',
            ]);
            
            $response->assertInvalid([
                'message' => trans('passwords.token')
            ]);

            return true;
        });
    }
}

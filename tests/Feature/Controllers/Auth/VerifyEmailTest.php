<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;

use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_is_not_verified_if_user_confirm_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertNotDispatched(Verified::class);
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }
    
    public function test_email_can_be_dispatched_repeated(): void
    {
        $user = User::factory()->create();
        
        Notification::fake();
        
        $response = $this->actingAs($user)->post('verify-email');
        
        Notification::assertSentTo([$user], VerifyEmail::class);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
    
    public function test_email_can_not_be_dispatched_repeated_if_user_confirm_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);
        
        Notification::fake();
        
        $response = $this->actingAs($user)->post('verify-email');
        
        Notification::assertNotSentTo([$user], VerifyEmail::class);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}

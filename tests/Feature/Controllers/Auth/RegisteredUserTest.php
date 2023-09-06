<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisteredUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('register');
        $this->assertGuest();

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Guest/Register')
                        ->has('errors', 0)
            );
    }

    public function test_new_users_can_register(): void
    {
        Event::fake();
        
        $response = $this->post('register', [
            'login' => 'TestLogin',
            'email' => 'testlogin@example.com',
            'password' => 'TestPassword7',
            'password_confirmation' => 'TestPassword7',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        
        // Отправляется событие, которое отправляет email для подтверждения адреса эл. почты
        Event::assertDispatched(Registered::class, 1);
    }

    public function test_new_users_can_not_register_with_invalid_login_unique(): void
    {
        Event::fake();
        
        $this->seed([
            \Database\Seeders\Person\BaseTestUserSeeder::class,
        ]);
        
        // Вводиться существующий логин
        $response = $this->post('register', [
            'login' => 'BaseTestLogin',
            'email' => 'testlogin@example.com',
            'password' => 'TestPassword7',
            'password_confirmation' => 'TestPassword7',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'login' => trans("auth.unique.login")
            ]);
        
        // Событие, которое должно отправить email для подтверждения адреса эл. почты, не отправляется
        Event::assertNotDispatched(Registered::class);
    }

    public function test_new_users_can_not_register_with_invalid_email_unique(): void
    {
        Event::fake();
        
        $this->seed([
            \Database\Seeders\Person\BaseTestUserSeeder::class,
        ]);
        
        // Вводиться существующая почта
        $response = $this->post('register', [
            'login' => 'TestLogin',
            // Задаётся почта пользователя BaseTestLogin
            'email' => 'basetestlogin@example.com',
            'password' => 'TestPassword7',
            'password_confirmation' => 'TestPassword7',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'email' => trans("auth.unique.email")
            ]);
        
        // Событие, которое должно отправить email для подтверждения адреса эл. почты, не отправляется
        Event::assertNotDispatched(Registered::class);
    }

    public function test_new_users_can_not_register_if_login_contains_not_only_latin_letters_or_numbers(): void
    {
        $response = $this->post('register', [
            // Логин содержит не латинские буквы
            'login' => 'Пользователь',
            'email' => 'testlogin@example.com',
            'password' => 'TestPassword7',
            'password_confirmation' => 'TestPassword7',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'login' => trans("auth.word.login")
            ]);
    }

    public function test_new_users_can_not_register_with_invalid_login_capitalfirst(): void
    {
        $response = $this->post('register', [
            // Логин начинается не с заглавной буквы
            'login' => 'testLogin',
            'email' => 'testlogin@example.com',
            'password' => 'TestPassword7',
            'password_confirmation' => 'TestPassword7',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'login' => trans("auth.capitalfirst.login")
            ]);
    }

    public function test_new_users_can_not_register_with_invalid_password_confirmation(): void
    {
        $response = $this->post('register', [
            'login' => 'TestLogin',
            'email' => 'testlogin@example.com',
            'password' => 'TestPassword7',
            // Подтверждение не совпадает с паролем
            'password_confirmation' => 'TestPassword',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'password' => trans("auth.confirmed.password")
            ]);
    }

    public function test_new_users_can_not_register_if_password_without_mixed_case(): void
    {
        $response = $this->post('register', [
            'login' => 'TestLogin',
            'email' => 'testlogin@example.com',
            // Пароль не имеет заглавной латинской буквы
            'password' => 'testpassword7',
            'password_confirmation' => 'testpassword7',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'password' => trans("auth.mixed.password")
            ]);
    }

    public function test_new_users_can_not_register_if_password_without_number(): void
    {
        $response = $this->post('register', [
            'login' => 'TestLogin',
            'email' => 'testlogin@example.com',
            // Пароль не имеет цифры
            'password' => 'TestPassword',
            'password_confirmation' => 'TestPassword',
        ]);

        $this->assertGuest();
        $response->assertInvalid([
                'password' => trans("auth.numbers.password")
            ]);
    }

    public function test_user_can_remove_his_account(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        $this->assertAuthenticated();
        
        $response = $acting->delete('register', [
            'password' => 'TestPassword7',
        ]);

        $this->assertGuest();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_user_can_not_remove_his_account_with_invalid_password(): void
    {
        $user = User::factory()->create();
        $acting = $this->actingAs($user);
        
        $response = $acting->delete('register', [
            'password' => 'wrongPassword',
        ]);

        $this->assertAuthenticated();
        $response->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
}

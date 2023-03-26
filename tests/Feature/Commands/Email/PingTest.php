<?php

namespace Tests\Feature\Commands\Email;

use App\Mail\Ping;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_email_can_be_sent(): void
    {
        // Блокируем реальную отправку email
        Mail::fake();
        
        // Ещё ничего не отправлено
        Mail::assertNothingSent();
        
        // Команда по отправке email завершилась успешно
        $this
            ->artisan('email:ping')
            ->assertExitCode(0);
        
        // Письмо отправлено
        Mail::assertSent(Ping::class, function (Ping $mail) {
            return $mail->hasTo(env('MAIL_TO_ADDRESS'));
        });
    }
    
    public function test_mailable_content(): void
    {
        $mailable = new Ping;

        $mailable->assertHasSubject('Проверка связи');
        
        $mailable->assertSeeInHtml('Почта работает отлично');
    }
}

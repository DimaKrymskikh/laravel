<?php

namespace Tests\Unit\Notifications\Dvdrental;

use App\Events\AddFilmInUserList;
use App\Notifications\Dvdrental\AddFilmNotification;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\TestCase;

class AddFilmNotificationTest extends TestCase
{
    private AddFilmInUserList $event;
    private AddFilmNotification $notification;

    public function test_via(): void
    {
        $notifiable = (object) [];
        
        $this->assertEquals(['mail'], $this->notification->via($notifiable));
    }

    public function test_to_mail(): void
    {
        $notifiable = (object) [
            'login' => 'TestLoginInAddFilmNotification',
        ];
        
        $this->event->expects($this->once())
                ->method('getFilmTitle')
                ->willReturn('TestTitleAddFilm');
        
        $this->assertInstanceOf(MailMessage::class, $this->notification->toMail($notifiable));
    }
    
    protected function setUp(): void
    {
        $this->event = $this->createMock(AddFilmInUserList::class);
        
        $this->notification = new AddFilmNotification($this->event);
    }
}

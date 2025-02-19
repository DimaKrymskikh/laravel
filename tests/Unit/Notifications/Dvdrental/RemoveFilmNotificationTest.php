<?php

namespace Tests\Unit\Notifications\Dvdrental;

use App\Events\RemoveFilmFromUserList;
use App\Notifications\Dvdrental\RemoveFilmNotification;
use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\TestCase;

class RemoveFilmNotificationTest extends TestCase
{
    private RemoveFilmFromUserList $event;
    private RemoveFilmNotification $notification;

    public function test_via(): void
    {
        $notifiable = (object) [];
        
        $this->assertEquals(['mail'], $this->notification->via($notifiable));
    }

    public function test_to_mail(): void
    {
        $notifiable = (object) [
            'login' => 'TestLoginInRemoveFilmNotification',
        ];
        
        $this->event->expects($this->once())
                ->method('getFilmTitle')
                ->willReturn('TestTitleRemoveFilm');
        
        $this->assertInstanceOf(MailMessage::class, $this->notification->toMail($notifiable));
    }
    
    protected function setUp(): void
    {
        $this->event = $this->createMock(RemoveFilmFromUserList::class);
        
        $this->notification = new RemoveFilmNotification($this->event);
    }
}

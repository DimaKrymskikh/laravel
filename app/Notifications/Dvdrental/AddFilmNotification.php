<?php

namespace App\Notifications\Dvdrental;

use App\Events\AddFilmInUserList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddFilmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
            private AddFilmInUserList $event,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Добавление фильма')
                    ->view(
                        'notifications.dvdrental.addfilm', [
                            'login' => $notifiable->login,
                            'title' => $this->event->getFilmTitle(),
                        ]
                    );
    }
}

<?php

namespace App\Notifications\Dvdrental;

use App\Events\RemoveFilmFromUserList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RemoveFilmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
            private RemoveFilmFromUserList $event
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
                    ->subject('Удаление фильма')
                    ->view(
                        'notifications.dvdrental.removefilm', [
                            'login' => $notifiable->login,
                            'title' => $this->event->getFilmTitle(),
                        ]
                    );
    }
}

<?php

namespace App\Notifications\Dvdrental;

use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddFilmNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    private string $title;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserFilm $userFilm)
    {
        $this->title = Film::find($userFilm->film_id)->title;
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
                            'title' => $this->title,
                        ]
                    );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

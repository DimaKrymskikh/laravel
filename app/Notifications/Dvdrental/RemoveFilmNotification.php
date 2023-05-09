<?php

namespace App\Notifications\Dvdrental;

use App\Models\Person\UserFilm;
use App\Models\ModelsFields;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RemoveFilmNotification extends Notification implements ShouldQueue
{
    use Queueable, ModelsFields;
    
    private string $title;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserFilm $userFilm)
    {
        $this->title = $this->getFilmTitle($userFilm->film_id);
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
                            'title' => $this->title
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

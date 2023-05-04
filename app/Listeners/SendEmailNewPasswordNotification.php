<?php

namespace App\Listeners;

use App\Notifications\Auth\NewPasswordNotification;

use Illuminate\Auth\Events\PasswordReset;

class SendEmailNewPasswordNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Отправляет пользователю письмо об успешной смене пароля.
     * 
     * @param PasswordReset $event
     * @return void
     */
    public function handle(PasswordReset $event): void
    {
        $event->user->notify(new NewPasswordNotification);
    }
}

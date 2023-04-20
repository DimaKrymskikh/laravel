<?php

namespace App\Listeners;

use App\Events\AddFilm;
use App\Models\UserTrait;
use App\Notifications\Dvdrental\AddFilmNotification;

class SendEmailAddFilmNotification
{
    use UserTrait;
    
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AddFilm $event): void
    {
        $user = $this->getUserWithFilm($event->userFilm);
        
        $user->notify(new AddFilmNotification);
    }
}

<?php

namespace App\Listeners;

use App\Events\RemoveFilm;
use App\Models\UserTrait;
use App\Notifications\Dvdrental\RemoveFilmNotification;

class SendEmailRemoveFilmNotification
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
    public function handle(RemoveFilm $event): void
    {
        $user = $this->getUserWithFilm($event->userFilm);
        
        $user->notify(new RemoveFilmNotification);
    }
}

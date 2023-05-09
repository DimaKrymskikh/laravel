<?php

namespace App\Listeners;

use App\Events\RemoveFilm;
use App\Models\User;
use App\Notifications\Dvdrental\RemoveFilmNotification;

use Illuminate\Support\Facades\Auth;

class SendEmailRemoveFilmNotification
{
    private User $user;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    /**
     * Handle the event.
     */
    public function handle(RemoveFilm $event): void
    {
        $this->user->notify(new RemoveFilmNotification($event->userFilm));
    }
}

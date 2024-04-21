<?php

namespace App\Listeners;

use App\Events\RemoveFilmFromUserList;
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
    public function handle(RemoveFilmFromUserList $event): void
    {
        $this->user->notify(new RemoveFilmNotification($event->film_id));
    }
}

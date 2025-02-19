<?php

namespace App\Listeners;

use App\Events\AddFilmInUserList;
use App\Models\User;
use App\Notifications\Dvdrental\AddFilmNotification;

use Illuminate\Support\Facades\Auth;

class SendEmailAddFilmNotification
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
    public function handle(AddFilmInUserList $event): void
    {
        $this->user->notify(new AddFilmNotification($event));
    }
}

<?php

namespace App\Providers;

use App\Events\AddFilmInUserList;
use App\Events\RemoveFilmFromUserList;
use App\Listeners\SendEmailAddFilmNotification;
use App\Listeners\SendEmailNewPasswordNotification;
use App\Listeners\SendEmailRemoveFilmNotification;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PasswordReset::class => [
            SendEmailNewPasswordNotification::class,
        ],
        AddFilmInUserList::class => [
            SendEmailAddFilmNotification::class
        ],
        RemoveFilmFromUserList::class => [
            SendEmailRemoveFilmNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

<?php

namespace App\Providers\BindingInterfaces;

use App\Modifiers\Dvd\Films\FilmModifiers;
use App\Modifiers\Dvd\Films\FilmModifiersInterface;
use Illuminate\Support\ServiceProvider;

class ModifiersProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FilmModifiersInterface::class, FilmModifiers::class);
    }
}

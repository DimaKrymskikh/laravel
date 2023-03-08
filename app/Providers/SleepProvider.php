<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Задерживает запрос (полезно при тестировании фронтенда).
 */
class SleepProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        sleep(1);
    }
}

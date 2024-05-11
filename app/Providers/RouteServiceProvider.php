<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';
    
    public const URL_ADMIN_ACTORS = 'admin/actors';
    public const URL_ADMIN_CITIES = 'admin/cities';
    public const URL_ADMIN_FILMS = 'admin/films';
    public const URL_ADMIN_LANGUAGES = 'admin/languages';
    public const URL_ADMIN_TIMEZONE = 'admin/timezone';
    
    public const URL_AUTH_FILMS = 'films';
    public const URL_AUTH_USERFILMS = 'userfilms';
    
    public const URL_GUEST_CITIES = 'guest/cities';
    public const URL_GUEST_FILMS = 'guest/films';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        
        Route::pattern('id', '[0-9]+');
        Route::pattern('city_id', '[0-9]+');
        Route::pattern('film_id', '[0-9]+');
        Route::pattern('timezone_id', '[0-9]+');
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}

<?php

namespace App\Providers\BindingInterfaces;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Http\Controllers\Project\Admin\Content\FilmController as AdminFilmController;
use App\Http\Controllers\Project\Auth\Account\UserFilmsController;
use App\Http\Controllers\Project\Auth\Content\FilmController as AuthFilmController;
use App\Http\Controllers\Project\Guest\Content\FilmController as GuestFilmController;
use App\Queries\Dvd\Films\AdminFilmsListForPageQueries;
use App\Queries\Dvd\Films\AuthFilmsListForPageQueries;
use App\Queries\Dvd\Films\GuestFilmsListForPageQueries;
use App\Queries\Dvd\Films\UserFilmsListForPageQueries;
use App\Queries\Thesaurus\Languages\LanguageQueries;
use App\Queries\Thesaurus\Languages\LanguageQueriesInterface;
use App\Support\Pagination\Dvd\FilmPagination;
use Illuminate\Support\ServiceProvider;

class QueriesProvider extends ServiceProvider
{
    const DEFAULT_LIMIT = 100;
    
    /**
     * Register services.
     */
    public function register(): void
    {
        $filmPagination = new FilmPagination();
        
        $this->app->when(GuestFilmController::class)
            ->needs(FilmsListForPageCommandHandler::class)
            ->give(function () use ($filmPagination) {
                $paginator = new GuestFilmsListForPageQueries($filmPagination);
                return new FilmsListForPageCommandHandler($paginator);
            });
        
        $this->app->when(AuthFilmController::class)
            ->needs(FilmsListForPageCommandHandler::class)
            ->give(function () use ($filmPagination) {
                $paginator = new AuthFilmsListForPageQueries($filmPagination);
                return new FilmsListForPageCommandHandler($paginator);
            });
        
        $this->app->when(UserFilmsController::class)
            ->needs(FilmsListForPageCommandHandler::class)
            ->give(function () use ($filmPagination) {
                $paginator = new UserFilmsListForPageQueries($filmPagination);
                return new FilmsListForPageCommandHandler($paginator);
            });
        
        $this->app->when(AdminFilmController::class)
            ->needs(FilmsListForPageCommandHandler::class)
            ->give(function () use ($filmPagination) {
                $paginator = new AdminFilmsListForPageQueries($filmPagination);
                return new FilmsListForPageCommandHandler($paginator);
            });
        
        $this->app->bind(LanguageQueriesInterface::class, LanguageQueries::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}

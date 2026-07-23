<?php

namespace App\Providers\BindingInterfaces\Films;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Http\Controllers\Project\Admin\Content\FilmController as AdminFilmController;
use App\Http\Controllers\Project\Auth\Account\UserFilmsController;
use App\Http\Controllers\Project\Auth\Content\FilmController as AuthFilmController;
use App\Http\Controllers\Project\Guest\Content\FilmController as GuestFilmController;
use App\Queries\Dvd\FilmsListForPage\AdminFilmsListForPageQueries;
use App\Queries\Dvd\FilmsListForPage\AuthFilmsListForPageQueries;
use App\Queries\Dvd\FilmsListForPage\GuestFilmsListForPageQueries;
use App\Queries\Dvd\FilmsListForPage\UserFilmsListForPageQueries;
use App\Support\Pagination\Dvd\FilmPagination;
use Illuminate\Support\ServiceProvider;

class FilmsListForPageProvider extends ServiceProvider
{
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
    }
}

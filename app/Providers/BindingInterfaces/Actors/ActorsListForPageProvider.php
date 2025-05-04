<?php

namespace App\Providers\BindingInterfaces\Actors;

use App\CommandHandlers\Database\Dvd\Actors\ActorsListForPageCommandHandler;
use App\Http\Controllers\Project\Admin\Content\ActorController;
use App\Queries\Dvd\Actors\ActorsListForPage\AdminActorsListForPageQueries;
use App\Support\Pagination\Dvd\ActorPagination;
use Illuminate\Support\ServiceProvider;

class ActorsListForPageProvider extends ServiceProvider
{
    public function register(): void
    {
        $actorPagination = new ActorPagination();
        
        $this->app->when(ActorController::class)
                ->needs(ActorsListForPageCommandHandler::class)
                ->give(function () use ($actorPagination) {
                    $paginator = new AdminActorsListForPageQueries($actorPagination);
                    return new ActorsListForPageCommandHandler($paginator);
                });
    }
}

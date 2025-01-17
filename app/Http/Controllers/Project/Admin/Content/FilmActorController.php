<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\ActorFilterRequest;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Dvd\ActorService;
use App\Services\Database\Dvd\FilmActorService;
use App\Support\Pagination\Urls\FilmUrls;
use Illuminate\Http\RedirectResponse;

class FilmActorController extends Controller
{
    public function __construct(
        private ActorService $actorService,
        private FilmActorService $filmActorService,
        private FilmUrls $filmUrls,
    ) {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * Возвращает список актёров с фильтром поиска для модального окна, в котором добавляется актёр в фильм
     * 
     * @param ActorFilterRequest $request
     * @return string
     */
    public function index(ActorFilterRequest $request): string
    {
        $actors = $this->actorService->getAllActorsList($request->getActorFilterDto());
        $filmActors = $this->filmActorService->getActorsListByFilmId($request->film_id);
        
        return (string) $actors->except($filmActors->modelKeys());
    }

    /**
     * В фильм $request->film_id добавляется актёр $request->actor_id
     * 
     * @param FilmFilterRequest $request
     * @return RedirectResponse
     */
    public function store(FilmFilterRequest $request): RedirectResponse
    {
        $this->filmActorService->create($request->film_id, $request->actor_id);
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsByRequest(
                    RouteServiceProvider::URL_ADMIN_FILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto()
                ));
    }

    /**
     * У фильма $request->film_id удаляется актёр $actorId
     * 
     * @param FilmFilterRequest $request
     * @param int $actorId
     * @return RedirectResponse
     */
    public function destroy(FilmFilterRequest $request, int $actorId): RedirectResponse
    {
        $this->filmActorService->delete($request->film_id, $actorId);
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsByRequest(
                    RouteServiceProvider::URL_ADMIN_FILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto()
                ));
    }
}

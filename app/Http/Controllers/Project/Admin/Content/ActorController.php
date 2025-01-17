<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\ActorFilterRequest;
use App\Http\Requests\Dvd\ActorRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Dvd\ActorService;
use App\Support\Pagination\Urls\ActorUrls;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ActorController extends Controller
{
    public function __construct(
        private ActorService $actorService,
        private ActorUrls $actorUrls,
    ) {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * В админской части отрисовывает таблицу актёров
     * 
     * @param ActorFilterRequest $request
     * @return Response
     */
    public function index(ActorFilterRequest $request): Response
    {
        return Inertia::render('Admin/Actors', [
            'actors' => $this->actorService->getActorsListForPage($request->getPaginatorDto(), $request->getActorFilterDto())
        ]);
    }

    /**
     * В таблицу 'dvd.actors' добавляется новый актёр
     * 
     * @param ActorRequest $request
     * @return RedirectResponse
     */
    public function store(ActorRequest $request): RedirectResponse
    {
        // Создаём новую запись в таблице 'thesaurus.languages'
        // (Валидация уже выполнена в ActorRequest)
        $actor = $this->actorService->create($request->getActorDto());
        
        return redirect($this->actorUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingActor(
                    RouteServiceProvider::URL_ADMIN_ACTORS,
                    $request->getPaginatorDto(),
                    $actor->id
                ));
    }

    /**
     * Изменяет полное имя актёра с id = $actorId
     * 
     * @param ActorRequest $request
     * @param int $actorId
     * @return RedirectResponse
     */
    public function update(ActorRequest $request, int $actorId): RedirectResponse
    {
        $this->actorService->update($request->getActorDto(), $actorId);
        
        return redirect($this->actorUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingActor(
                    RouteServiceProvider::URL_ADMIN_ACTORS,
                    $request->getPaginatorDto(),
                    $actorId
                ));
    }

    /**
     * Удаляет актёра с id = $actorId из таблицы 'dvd.actors'
     * 
     * @param ActorFilterRequest $request
     * @param int $actorId
     * @return RedirectResponse
     */
    public function destroy(ActorFilterRequest $request, int $actorId): RedirectResponse
    {
        $this->actorService->delete($actorId);
        
        return redirect($this->actorUrls->getUrlWithPaginationOptionsAfterRemovingActor(
                    RouteServiceProvider::URL_ADMIN_ACTORS,
                    $request->getPaginatorDto(),
                    $request->getActorFilterDto()
                ));
    }
}

<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\ActorRequest;
use App\Providers\RouteServiceProvider;
use App\Repositories\Dvd\ActorRepository;
use App\Services\Database\Dvd\ActorService;
use App\Support\Pagination\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActorController extends Controller
{
    private Url $url;
    
    public function __construct(
        private ActorRepository $actors,
    )
    {
        $this->middleware('check.password')->only('destroy');
        $this->url = new Url(ActorRepository::ADDITIONAL_PARAMS_IN_URL);
    }
    
    /**
     * В админской части отрисовывает таблицу актёров
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Actors', [
            'actors' => $this->actors->getCommonActorsList($request)
        ]);
    }

    /**
     * В таблицу 'dvd.actors' добавляется новый актёр
     * 
     * @param ActorRequest $request
     * @param ActorService $actorService
     * @return RedirectResponse
     */
    public function store(ActorRequest $request, ActorService $actorService): RedirectResponse
    {
        // Создаём новую запись в таблице 'thesaurus.languages'
        // (Валидация уже выполнена в ActorRequest)
        $actor = $actorService->create($request->getActorDto());
        
        // Нужно сбросить фильтр поиска, чтобы новый актёр гарантированно попал в список актёров
        $request->name = '';
        
        return redirect($this->url->getUrlByItem(RouteServiceProvider::URL_ADMIN_ACTORS, $request, $this->actors, $actor->id));
    }

    /**
     * Изменяет полное имя актёра с id = $actor_id
     * 
     * @param ActorRequest $request
     * @param ActorService $actorService
     * @param int $actor_id
     * @return RedirectResponse
     */
    public function update(ActorRequest $request, ActorService $actorService, int $actor_id): RedirectResponse
    {
        $actorService->update($request->getActorDto(), $actor_id);
        
        return redirect($this->url->getUrlByItem(RouteServiceProvider::URL_ADMIN_ACTORS, $request, $this->actors, $actor_id));
    }

    /**
     * Удаляет актёра с id = $actor_id из таблицы 'dvd.actors'
     * 
     * @param Request $request
     * @param ActorService $actorService
     * @param int $actor_id
     * @return RedirectResponse
     */
    public function destroy(Request $request, ActorService $actorService, int $actor_id): RedirectResponse
    {
        $actorService->delete($actor_id);
        
        return redirect($this->url->getUrlAfterRemovingItem(RouteServiceProvider::URL_ADMIN_ACTORS, $request, $this->actors));
    }
}

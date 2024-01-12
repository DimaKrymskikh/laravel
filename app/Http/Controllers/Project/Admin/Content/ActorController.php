<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\ActorRequest;
use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use App\Providers\RouteServiceProvider;
use App\Repositories\Dvd\ActorRepository;
use App\Support\Pagination\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * @return RedirectResponse
     */
    public function store(ActorRequest $request): RedirectResponse
    {
        // Создаём новую запись в таблице 'thesaurus.languages'
        $actor = new Actor();
        $actor->first_name = $request->first_name;
        $actor->last_name = $request->last_name;
        $actor->save();
        
        // Нужно сбросить фильтр поиска, чтобы новый актёр гарантированно попал в список актёров
        $request->name = '';
        
        return redirect($this->url->getUrlByItem(RouteServiceProvider::URL_ADMIN_ACTORS, $request, $this->actors, $actor->id));
    }

    /**
     * Изменяет полное имя актёра с id = $actor_id
     * 
     * @param ActorRequest $request
     * @param int $actor_id
     * @return RedirectResponse
     */
    public function update(ActorRequest $request, int $actor_id): RedirectResponse
    {
        $actor = Actor::find($actor_id);
        $actor->first_name = $request->first_name;
        $actor->last_name = $request->last_name;
        $actor->save();
        
        return redirect($this->url->getUrlByRequest(RouteServiceProvider::URL_ADMIN_ACTORS, $request));
    }

    /**
     * Удаляет актёра с id = $actor_id из таблицы 'dvd.actors'
     * 
     * @param Request $request
     * @param int $actor_id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $actor_id): RedirectResponse
    {
        DB::transaction(function () use ($actor_id) {
            FilmActor::where('actor_id', $actor_id)->delete();
            Actor::find($actor_id)->delete();
        });
        
        return redirect($this->url->getUrlAfterRemovingItem(RouteServiceProvider::URL_ADMIN_ACTORS, $request, $this->actors));
    }
}

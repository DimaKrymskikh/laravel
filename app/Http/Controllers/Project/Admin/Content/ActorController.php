<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Http\Extraction\Dvd\Actors;
use App\Http\Requests\Dvd\ActorRequest;
use App\Models\Dvd\Actor;
use App\Models\Dvd\FilmActor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ActorController extends Controller
{
    use Actors, Url;
    
    public function __construct()
    {
        $this->middleware('check.password')->only('destroy');
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
            'actors' => $this->getCommonActorsList($request)
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
        
        return redirect($this->getUrl('admin/actors', [
            'page' => $this->getCurrentPageBySerialNumber($request, $this->getSerialNumberOfTheActorInTheList($request, $actor->id)),
            'number' => $request->number,
            'name' => $request->name,
        ]));
    }

    /**
     * Изменяет полное имя актёра с id = $id
     * 
     * @param ActorRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(ActorRequest $request, string $id): RedirectResponse
    {
        $actor = Actor::find($id);
        $actor->first_name = $request->first_name;
        $actor->last_name = $request->last_name;
        $actor->save();
        
        return redirect($this->getUrl('admin/actors', [
            'page' => $request->page,
            'number' => $request->number,
            'name' => $request->name,
        ]));
    }

    /**
     * Удаляет актёра с id = $id из таблицы 'dvd.actors'
     * 
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            FilmActor::where('actor_id', $id)->delete();
            Actor::find($id)->delete();
        });
        
        return redirect($this->getUrl('admin/actors', [
            'page' => $this->getCurrentPageAfterRemovingItems($request, Actor::all()->count()),
            'number' => $request->number,
            'name' => $request->name,
        ]));
    }
}

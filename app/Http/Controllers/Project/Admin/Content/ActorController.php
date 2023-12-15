<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Models\Dvd\Actor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Http\Extraction\Dvd\Actors;
use App\Http\Requests\Dvd\ActorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Actors', [
            'actors' => $this->getCommonActorsList($request)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ActorRequest $request): RedirectResponse
    {
        // Создаём новую запись в таблице 'thesaurus.languages'
        $actor = new Actor();
        $actor->first_name = $request->first_name;
        $actor->last_name = $request->last_name;
        $actor->save();
        
        return redirect($this->getUrl('admin/actors', [
            'page' => $this->getCurrentPageBySerialNumber($request, $this->getSerialNumberOfTheActorInTheList($actor->id)),
            'number' => $request->number,
            'name' => $request->name,
        ]));
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        Actor::find($id)->delete();
        
        return redirect($this->getUrl('admin/actors', [
            'page' => $this->getCurrentPageAfterRemovingItems($request, Actor::all()->count()),
            'number' => $request->number,
            'name' => $request->name,
        ]));
    }
}

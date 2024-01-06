<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Http\Extraction\Dvd\Actors;
use App\Models\Dvd\Actor;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FilmActorController extends Controller
{
    use Actors, Url;
    
    public function __construct()
    {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * Возвращает список актёров с фильтром поиска для модального окна, в котором добавляется актёр в фильм
     * 
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $actors = $this->getCommonActorsList($request, false);
        $filmActors = FilmActor::where('film_id', $request->film_id)->get();
        
        return (string) $actors->except($filmActors->modelKeys());
    }

    /**
     * В фильм $request->film_id добавляется актёр $request->actor_id
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (
            FilmActor::where('film_id', '=', $request->film_id)
                ->where('actor_id', '=', $request->actor_id)
                ->exists()
        ) {
            // Если пара существует, выбрасываем исключение
            $film = Film::find($request->film_id);
            $actor = Actor::find($request->actor_id);
            throw ValidationException::withMessages([
                'message' => trans("attr.film.contains.actor", [
                    'film' => $film->title,
                    'actor' => "$actor->first_name $actor->last_name",
                ]),
            ]);
        }
        
        $filmActor = new FilmActor();
        $filmActor->film_id = $request->film_id;
        $filmActor->actor_id = $request->actor_id;
        $filmActor->save();
        
        return redirect($this->getUrl('admin/films', [
            'page' => $request->page,
            'number' => $request->number,
            'title_filter' => $request->title_filter,
            'description_filter' => $request->description_filter,
        ]));
    }

    /**
     * У фильма $request->film_id удаляется актёр
     * 
     * @param Request $request
     * @param string $id - id актёра
     * @return RedirectResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        FilmActor::where('actor_id', $id)
                ->where('film_id', $request->film_id)
                ->delete();
        
        return redirect($this->getUrl('admin/films', [
            'page' => $request->page,
            'number' => $request->number,
            'title_filter' => $request->title_filter,
            'description_filter' => $request->description_filter,
        ]));
    }
}

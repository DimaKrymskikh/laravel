<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Models\Dvd\Actor;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use App\Providers\RouteServiceProvider;
use App\Repositories\Dvd\ActorRepository;
use App\Repositories\Dvd\FilmRepository;
use App\Support\Pagination\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FilmActorController extends Controller
{
    private Url $url;

    public function __construct(
        private ActorRepository $actors,
    )
    {
        $this->middleware('check.password')->only('destroy');
        $this->url = new Url(FilmRepository::ADDITIONAL_PARAMS_IN_URL);
    }
    
    /**
     * Возвращает список актёров с фильтром поиска для модального окна, в котором добавляется актёр в фильм
     * 
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $actors = $this->actors->getCommonActorsList($request, false);
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
        
        return redirect($this->url->getUrlByRequest(RouteServiceProvider::URL_ADMIN_FILMS, $request));
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
        
        return redirect($this->url->getUrlByRequest(RouteServiceProvider::URL_ADMIN_FILMS, $request));
    }
}

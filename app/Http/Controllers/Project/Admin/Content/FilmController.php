<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\FilmRequest;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use App\Providers\RouteServiceProvider;
use App\Repositories\Dvd\FilmRepository;
use App\Support\Pagination\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    private Url $url;

    public function __construct(
        private FilmRepository $films,
    )
    {
        $this->middleware('check.password')->only('destroy');
        $this->url = new Url(FilmRepository::ADDITIONAL_PARAMS_IN_URL);
    }
    
    /**
     * В админской части отрисовывает таблицу фильмов
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Films', [
                'films' => $this->films->getCommonFilmsListWithActors($request)
            ]);
    }

    /**
     * В таблицу 'dvd.films' добавляется новый фильм
     * 
     * @param FilmRequest $request
     * @return RedirectResponse
     */
    public function store(FilmRequest $request): RedirectResponse
    {
        $film = new Film();
        $film->title = $request->title;
        $film->description = $request->description;
        $film->release_year = $request->release_year;
        $film->save();
        
        // Сбрасываем фильтр поиска, чтобы новый фильм гарантированно попал в список фильмов
        $request->title_filter = '';
        $request->description_filter = '';
        $request->release_year_filter = '';
        
        return redirect($this->url->getUrlByItem(RouteServiceProvider::URL_ADMIN_FILMS, $request, $this->films, $film->id));
    }

    /**
     * Изменяет данные фильма с id = $film_id
     * 
     * @param FilmRequest $request
     * @param int $film_id
     * @return RedirectResponse
     */
    public function update(FilmRequest $request, int $film_id): RedirectResponse
    {
        $field = $request->field;
        
        $film = Film::find($film_id);
        $film->$field = $request->$field;
        $film->save();
        
        return redirect($this->url->getUrlByItem(RouteServiceProvider::URL_ADMIN_FILMS, $request, $this->films, $film_id));
    }

    /**
     * Удаляет фильм с id = $film_id из таблицы 'dvd.films'
     * 
     * @param Request $request
     * @param int $film_id
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $film_id): RedirectResponse
    {
        DB::transaction(function () use ($film_id) {
            FilmActor::where('film_id', $film_id)->delete();
            Film::find($film_id)->delete();
        });
        
        return redirect($this->url->getUrlAfterRemovingItem(RouteServiceProvider::URL_ADMIN_FILMS, $request, $this->films));
    }
    
    /**
     * Возвращает список актёров фильма в формате json
     * 
     * @param int $film_id
     * @return string
     */
    public function getActorsList(int $film_id): string
    {
        return (string) $this->films->getActorsList($film_id);
    }
}

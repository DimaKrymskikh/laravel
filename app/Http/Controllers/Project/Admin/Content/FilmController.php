<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Url;
use App\Http\Extraction\Dvd\Films;
use App\Http\Requests\Dvd\FilmRequest;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    use Films, Url;
    
    public function __construct()
    {
        $this->middleware('check.password')->only('destroy');
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
                'films' => $this->getCommonFilmsListWithActors($request)
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
        
        return redirect($this->getUrl('admin/films', [
            'page' => $this->getCurrentPageBySerialNumber($request, $this->getSerialNumberOfTheFilmInTheList($request, $film->id)),
            'number' => $request->number,
            'title_filter' => $request->title_filter,
            'description_filter' => $request->description_filter,
        ]));
    }

    /**
     * Изменяет данные фильма с id = $id
     * 
     * @param FilmRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(FilmRequest $request, string $id): RedirectResponse
    {
        $field = $request->field;
        
        $film = Film::find($id);
        $film->$field = $request->$field;
        $film->save();
        
        return redirect($this->getUrl('admin/films', [
            'page' => $this->getCurrentPageBySerialNumber($request, $this->getSerialNumberOfTheFilmInTheList($request, $film->id)),
            'number' => $request->number,
            'title_filter' => $request->title_filter,
            'description_filter' => $request->description_filter,
        ]));
    }

    /**
     * Удаляет фильм с id = $id из таблицы 'dvd.films'
     * 
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            FilmActor::where('film_id', $id)->delete();
            Film::find($id)->delete();
        });
        
        return redirect($this->getUrl('admin/films', [
            'page' => $this->getCurrentPageAfterRemovingItems($request, $this->getCommonFilmsList($request, false)->count()),
            'number' => $request->number,
            'title_filter' => $request->title_filter,
            'description_filter' => $request->description_filter,
        ]));
    }
    
    /**
     * Возвращает список актёров фильма в формате json
     * 
     * @param string $film_id
     * @return string
     */
    public function getActorsList(string $film_id): string
    {
        return (string) Film::where('id', $film_id)
                            ->with([
                                'actors' => function (Builder $query) {
                                    $query->select('id', 'first_name', 'last_name')
                                        ->orderBy('first_name')
                                        ->orderBy('last_name');
                                }
                            ])
                            ->select('id')
                            ->first();
    }
}

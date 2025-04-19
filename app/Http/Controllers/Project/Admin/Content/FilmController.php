<?php

namespace App\Http\Controllers\Project\Admin\Content;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\Http\Requests\Dvd\FilmRequest;
use App\Providers\RouteServiceProvider;
use App\Services\Database\Dvd\FilmService;
use App\Support\Pagination\Urls\Films\FilmUrls;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    public function __construct(
            private FilmsListForPageCommandHandler $filmHandler,
            private FilmService $filmService,
        private FilmUrls $filmUrls
    ) {
        $this->middleware('check.password')->only('destroy');
    }
    
    /**
     * В админской части отрисовывает таблицу фильмов
     * 
     * @param FilmFilterRequest $request
     * @return Response
     */
    public function index(FilmFilterRequest $request): Response
    {
        return Inertia::render('Admin/Films', [
                'films' => $this->filmHandler->handle($request->getPaginatorDto(), $request->getFilmFilterDto())
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
        $film = $this->filmService->create($request->getFilmDto());
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm(
                    RouteServiceProvider::URL_ADMIN_FILMS,
                    $request->getPaginatorDto(),
                    $film->id
                ));
    }

    /**
     * Изменяет данные фильма с id = $filmId
     * 
     * @param FilmRequest $request
     * @param int $filmId
     * @return RedirectResponse
     */
    public function update(FilmRequest $request, int $filmId): RedirectResponse
    {
        $field = $request->field;
        $this->filmService->update($field, $request->$field, $filmId);
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsAfterCreatingOrUpdatingFilm(
                    RouteServiceProvider::URL_ADMIN_FILMS,
                    $request->getPaginatorDto(),
                    $filmId
                ));
    }

    /**
     * Удаляет фильм с id = $filmId из таблицы 'dvd.films'
     * 
     * @param FilmFilterRequest $request
     * @param int $filmId
     * @return RedirectResponse
     */
    public function destroy(FilmFilterRequest $request, int $filmId): RedirectResponse
    {
        $this->filmService->delete($filmId);
        
        return redirect($this->filmUrls->getUrlWithPaginationOptionsAfterRemovingFilm(
                    RouteServiceProvider::URL_ADMIN_FILMS,
                    $request->getPaginatorDto(),
                    $request->getFilmFilterDto()
                ));
    }
    
    /**
     * Возвращает список актёров фильма в формате json
     * 
     * @param int $filmId
     * @return string
     */
    public function getActorsList(int $filmId): string
    {
        return (string) $this->filmService->getActorsList($filmId);
    }
}

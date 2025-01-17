<?php

namespace App\Http\Controllers\Project\Guest\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\Services\Database\Dvd\FilmService;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    public function __construct(
        private FilmService $filmService,
    ) {
    }
    
    /**
     * Для гостя отрисовывает таблицу фильмов
     * 
     * @param FilmFilterRequest $request
     * @return Response
     */
    public function index(FilmFilterRequest $request): Response
    {
        return Inertia::render('Guest/Films', [
                'films' => $this->filmService->getFilmsListForPage($request->getPaginatorDto(), $request->getFilmFilterDto())
            ]);
    }
}

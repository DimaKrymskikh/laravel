<?php

namespace App\Http\Controllers\Project\Guest\Content;

use App\CommandHandlers\Database\Dvd\Films\FilmsListForPageCommandHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    public function __construct(
        private FilmsListForPageCommandHandler $filmHandler,
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
                'films' => $this->filmHandler->handle($request->getPaginatorDto(), $request->getFilmFilterDto()),
            ]);
    }
}

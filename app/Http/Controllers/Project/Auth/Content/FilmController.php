<?php

namespace App\Http\Controllers\Project\Auth\Content;

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
     * Для залогиненного пользователя отрисовывает таблицу фильмов с указанием о принадлежности фильма коллекции пользователя
     * 
     * @param FilmFilterRequest $request
     * @return Response
     */
    public function index(FilmFilterRequest $request): Response
    {
        $user = $request->user();
        
        return Inertia::render('Auth/Films', [
                'films' => $this->filmHandler->handle($request->getPaginatorDto(), $request->getFilmFilterDto(), $user->id),
                'user' => $user
            ]);
    }
}

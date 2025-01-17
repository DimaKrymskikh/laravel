<?php

namespace App\Http\Controllers\Project\Auth\Content;

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
     * Для залогиненного пользователя отрисовывает таблицу фильмов с указанием о принадлежности фильма коллекции пользователя
     * 
     * @param FilmFilterRequest $request
     * @return Response
     */
    public function index(FilmFilterRequest $request): Response
    {
        $user = $request->user();
        
        return Inertia::render('Auth/Films', [
                'films' => $this->filmService->getFilmsListWithAvailable($request->getPaginatorDto(), $request->getFilmFilterDto(), $user->id),
                'user' => $user
            ]);
    }
}

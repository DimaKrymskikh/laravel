<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dvd\Filters\FilmFilterRequest;
use App\Services\Database\Dvd\FilmService;

class FilmsController extends Controller
{
    public function __construct(
        private FilmService $filmService,
    ) {
    }
    
    /**
     * Отдаёт список фильмов пользователя
     * 
     * @param FilmFilterRequest $request
     * @return string
     */
    public function getFilms(FilmFilterRequest $request): string
    {
        return (string) collect([
            'films' => $this->filmService->getFilmsList($request->getFilmFilterDto())
        ]);
    }
    
    /**
     * Отдаёт карточку фильма
     * 
     * @param int $filmId
     * @return string
     */
    public function getFilm(int $filmId): string
    {
        return (string) collect([
            'film' => $this->filmService->getFilmCard($filmId)
        ]);
    }
}

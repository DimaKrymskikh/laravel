<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Extraction\Dvd\Films;
use App\Models\Dvd\Film;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class FilmsController extends Controller
{
    use Films;
    
    /**
     * Отдаёт список фильмов пользователя
     * 
     * @param Request $request
     * @return string
     */
    public function getFilms(Request $request): string
    {
        return json_encode([
            'films' => Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) use ($request) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', '=', $request->user()->id);
                })
                ->select('id', 'title', 'description', 'language_id')
                ->orderBy('title')
                ->get()
        ]);
    }
    
    /**
     * Отдаёт карточку фильма
     * 
     * @param int $film_id
     * @return string
     */
    public function getFilm(int $film_id): string
    {
        return json_encode([
            'film' => $this->getFilmCard($film_id)
        ]);
    }
}

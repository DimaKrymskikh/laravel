<?php

namespace App\Http\Controllers\Dvdrental;

use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;

class CommonController extends Controller
{
    public function home(): Response
    {
        return Inertia::render((Auth::check() ? 'Auth/' : 'Guest/') . 'Home');
    }
    
    public function catalog(): Response
    {
        $view = (Auth::check() ? 'Auth/' : 'Guest/') . 'Catalog';
        $query = Film::with('language:id,name');
        
        // Для аутентифицированного пользователя указывается принадлежность фильма его коллекции
        // Задаём связь с таблицей person.users_films
        if (Auth::check()) {
            $query = $query->leftJoin('person.users_films', function(JoinClause $join) {
                $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                    ->where('person.users_films.user_id', '=', Auth::id());
            });
        }
        
        $query = $query->select('id', 'title', 'description', 'language_id');
        
        // Для аутентифицированного пользователя принадлежность фильма его коллекции сохраняем в isAvailable
        if (Auth::check()) {
            $query = $query->selectRaw('coalesce (person.users_films.user_id::bool, false) AS "isAvailable"');
        }
        
        return Inertia::render($view, [
                'films' => $query->orderBy('title')->paginate(20),
            ]);
    }
}

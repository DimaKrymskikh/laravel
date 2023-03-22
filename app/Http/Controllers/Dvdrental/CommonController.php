<?php

namespace App\Http\Controllers\Dvdrental;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;
use App\Providers\RouteServiceProvider;

class CommonController extends Controller
{
    public function home(): Response
    {
        return Inertia::render((Auth::check() ? 'Auth' : 'Guest') . '/Home');
    }
    
    public function catalog(Request $request): Response
    {
        $view = (Auth::check() ? 'Auth' : 'Guest') . '/Catalog';
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
        
        $query = $query->when($request->title, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                });
   
        // Число фильмов на странице
        $perPage = $request->number ?? RouteServiceProvider::PAGINATE_DEFAULT_PER_PAGE;
        
        return Inertia::render($view, [
                'films' => $query->orderBy('title')->paginate($perPage)->appends([
                    'number' => $perPage,
                    'title' => $request->title,
                    'description' => $request->description
                ]),
            ]);
    }
}

<?php

namespace App\Http\Controllers\Project\Auth\Content;

use App\Http\Controllers\Controller;
use App\Models\Dvd\Film;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FilmController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Film::with('language:id,name')
                    ->leftJoin('person.users_films', function(JoinClause $join) use ($request) {
                        $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                            ->where('person.users_films.user_id', $request->user()->id);
                    })
                ->select('id', 'title', 'description', 'language_id')
                ->selectRaw('coalesce (person.users_films.user_id::bool, false) AS "isAvailable"')
                ->when($request->title, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                });
   
        // Число фильмов на странице
        $perPage = $request->number ?? RouteServiceProvider::PAGINATE_DEFAULT_PER_PAGE;
        
        return Inertia::render('Auth/Films', [
                'films' => $query->orderBy('title')->paginate($perPage)->appends([
                    'number' => $perPage,
                    'title' => $request->title,
                    'description' => $request->description
                ]),
                'user' => $request->user()
            ]);
    }
}

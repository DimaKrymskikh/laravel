<?php

namespace App\Http\Extraction\Dvd;

use App\Models\Dvd\Film;
use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

trait Films
{
    /**
     * Возвращает список фильмов из коллекции пользователя
     * 
     * @param Request $request
     * @return object
     */
    public function getFilmsList(Request $request): object
    {
        // Число фильмов на странице
        $perPage = $request->number ?? RouteServiceProvider::PAGINATE_DEFAULT_PER_PAGE;
        
        return Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) use ($request) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', '=', $request->user()->id);
                })
                ->select('id', 'title', 'description', 'language_id')
                ->when($request->title, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->orderBy('title')->paginate($perPage)->appends([
                    'number' => $perPage,
                    'title' => $request->title,
                    'description' => $request->description
                ]);
    }
}

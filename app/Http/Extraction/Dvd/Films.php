<?php

namespace App\Http\Extraction\Dvd;

use App\Http\Extraction\Pagination;
use App\Models\Dvd\Film;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

trait Films
{
    use Pagination;
    
    /**
     * Общий список фильмов
     * 
     * @param Request $request
     * @return object
     */
    private function getCommonFilmsList(Request $request): LengthAwarePaginator
    {
        $query = Film::with('language:id,name')
                ->select('id', 'title', 'description', 'language_id')
                ->when($request->title, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->orderBy('title');
                
        return $this->setPagination($query, $request);
    }
    
    /**
     * Возвращает список фильмов с пометкой, принадлежит фильм коллекции пользователя или нет
     * 
     * @param Request $request
     * @return object
     */
    private function getFilmsListWithAvailable(Request $request): LengthAwarePaginator
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
                })
                ->orderBy('title');
                
        return $this->setPagination($query, $request);
    }
    
    /**
     * Возвращает список фильмов из коллекции пользователя
     * 
     * @param Request $request
     * @return object
     */
    private function getUserFilmsList(Request $request): LengthAwarePaginator
    {
        $query = Film::with('language:id,name')
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
                ->orderBy('title');
                
        return $this->setPagination($query, $request);
    }
    
    /**
     * Возвращает карточку фильма по id фильма
     * 
     * @param int $film_id
     * @return object
     */
    private function getFilmCard(int $film_id): Film
    {
        return Film::with([
                'language:id,name',
                'actors:id,first_name,last_name'
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->where('id', '=', $film_id)
            ->first();
    }
    
    private function setPagination(Builder $query, Request $request): LengthAwarePaginator
    {
        $perPage = $this->getNumberPerPage($request);
        
        return $query->paginate($perPage)->appends([
                    'number' => $perPage,
                    'title' => $request->title,
                    'description' => $request->description
                ]);
    }
}

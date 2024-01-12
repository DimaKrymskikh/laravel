<?php

namespace App\Repositories\Dvd;

use App\Contracts\Repositories\ListItem;
use App\Models\Dvd\Film;
use App\Support\Pagination\Paginator;
use App\Support\Pagination\RequestGuard;
use Illuminate\Contracts\Database\Eloquent\Builder as ContractsBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FilmRepository implements ListItem
{
    use Paginator;
    
    public const ADDITIONAL_PARAMS_IN_URL = ['title_filter', 'description_filter', 'release_year_filter'];
    
    private RequestGuard $guard;
    
    public function __construct()
    {
        $this->guard = new RequestGuard(self::ADDITIONAL_PARAMS_IN_URL);
    }
    
    private function queryCommonFilmsList(Request $request): Builder
    {
        return Film::with('language:id,name')
                ->select(
                    'id',
                    'title',
                    'description',
                    'language_id',
                    'release_year',
                    DB::raw('row_number() OVER(ORDER BY title) AS n')
                )
                ->when($request->title_filter, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description_filter, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->when($request->release_year_filter, function (Builder $query, string $release_year) {
                    $query->where('release_year', 'ILIKE', "%$release_year%");
                })
                ->orderBy('title');
    }

    /**
     * Общий список фильмов
     * 
     * @param Request $request
     * @return object
     */
    public function getCommonFilmsList(Request $request, bool $isPagination = true): LengthAwarePaginator | Collection
    {
        $query = $this->queryCommonFilmsList($request);
                
        return $isPagination ? $this->setPagination($query, $request, $this->guard) : $query->get();
    }
    
    /**
     * Общий список фильмов с актёрами
     * 
     * @param Request $request
     * @return object
     */
    public function getCommonFilmsListWithActors(Request $request): LengthAwarePaginator
    {
        $query = Film::with('language:id,name')
                ->leftJoin('dvd.films_actors', 'dvd.films_actors.film_id', '=', 'dvd.films.id')
                ->leftJoin('dvd.actors', 'dvd.actors.id', '=', 'dvd.films_actors.actor_id')
                ->select(
                        'dvd.films.id',
                        'dvd.films.title',
                        'dvd.films.description',
                        'dvd.films.language_id',
                        'dvd.films.release_year',
                        DB::raw(<<<SQL
                                COALESCE(NULLIF(
                                    STRING_AGG(CONCAT(dvd.actors.first_name, ' ', dvd.actors.last_name), ', '), ' '
                                ), 'Актёры не добавлены') AS "actorsList"
                            SQL)
                    )
                ->when($request->title_filter, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description_filter, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->when($request->release_year_filter, function (Builder $query, string $release_year) {
                    $query->where('release_year', 'ILIKE', "%$release_year%");
                })
                ->groupBy('dvd.films.id')
                ->orderBy('title');
                
        return $this->setPagination($query, $request, $this->guard);
    }
    
    /**
     * Возвращает список фильмов с пометкой, принадлежит фильм коллекции пользователя или нет
     * 
     * @param Request $request
     * @return object
     */
    public function getFilmsListWithAvailable(Request $request): LengthAwarePaginator
    {
        $query = Film::with('language:id,name')
                    ->leftJoin('person.users_films', function(JoinClause $join) use ($request) {
                        $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                            ->where('person.users_films.user_id', $request->user()->id);
                    })
                ->select('id', 'title', 'description', 'language_id')
                ->selectRaw('coalesce (person.users_films.user_id::bool, false) AS "isAvailable"')
                ->when($request->title_filter, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description_filter, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->when($request->release_year_filter, function (Builder $query, string $release_year) {
                    $query->where('release_year', 'ILIKE', "%$release_year%");
                })
                ->orderBy('title');
                
        return $this->setPagination($query, $request, $this->guard);
    }
    
    /**
     * Возвращает список фильмов из коллекции пользователя
     * 
     * @param Request $request
     * @return object
     */
    public function getUserFilmsList(Request $request): LengthAwarePaginator
    {
        $query = Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) use ($request) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', '=', $request->user()->id);
                })
                ->select('id', 'title', 'description', 'language_id')
                ->when($request->title_filter, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description_filter, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->when($request->release_year_filter, function (Builder $query, string $release_year) {
                    $query->where('release_year', 'ILIKE', "%$release_year%");
                })
                ->orderBy('title');
                
        return $this->setPagination($query, $request, $this->guard);
    }
    
    /**
     * Возвращает карточку фильма по id фильма
     * 
     * @param int $film_id
     * @return object
     */
    public function getFilmCard(int $film_id): Film
    {
        return Film::with([
                'language:id,name',
                'actors:id,first_name,last_name'
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->where('id', '=', $film_id)
            ->first();
    }
    
    /**
     * Возвращает список актёров фильма с id = $film_id
     * 
     * @param int $film_id
     * @return Film
     */
    public function getActorsList(int $film_id): Film
    {
        return Film::where('id', $film_id)
                            ->with([
                                'actors' => function (ContractsBuilder $query) {
                                    $query->select('id', 'first_name', 'last_name')
                                        ->orderBy('first_name')
                                        ->orderBy('last_name');
                                }
                            ])
                            ->select('id')
                            ->first();
    }
    
    /**
     * Возвращает номер фильма в списке фильмов с фильтрами и с сортировкой по названию
     * 
     * @param Request $request
     * @param int $filmId
     * @return int
     */
    public function getSerialNumberOfItemInList(Request $request, int $filmId): int
    {
        $film = $this->queryCommonFilmsList($request)->get()->find($filmId);
        
        return $film ? $film->n : RequestGuard::DEFAULT_SERIAL_NUMBER;
    }
    
    /**
     * Возвращает общее число фильмов в списке фильмов с фильтрами и с сортировкой по названию
     * 
     * @param Request $request
     * @return int
     */
    public function getNumberOfItemsInList(Request $request): int
    {
        return $this->queryCommonFilmsList($request)->get()->count();
    }
}

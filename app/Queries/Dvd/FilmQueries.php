<?php

namespace App\Queries\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Models\Dvd\FilmActor;
use Illuminate\Contracts\Database\Eloquent\Builder as ContractsBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class FilmQueries
{
    public function getFilmById(int $filmId): Film
    {
        return Film::find($filmId);
    }
    
    public function getFilmsCount(FilmFilterDto $dto): int
    {
        return Film::filter($dto)->count();
    }
    
    public function delete(int $filmId): void
    {
        DB::transaction(function () use ($filmId) {
            FilmActor::where('film_id', $filmId)->delete();
            Film::find($filmId)->delete();
        });
    }
    
    public function queryFilmCard(int $filmId): Builder
    {
        return Film::with([
                'language:id,name',
                'actors:id,first_name,last_name'
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->where('id', '=', $filmId);
    }
    
    public function queryFilmsList(FilmFilterDto $dto): Builder
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
                ->filter($dto)
                ->orderBy('title');
    }
    
    public function queryUserFilmsList(FilmFilterDto $dto, int $userId): Builder
    {
        return Film::with('language:id,name')
                ->join('person.users_films', function(JoinClause $join) use ($userId) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', '=', $userId);
                })
                ->select('id', 'title', 'description', 'language_id')
                ->filter($dto)
                ->orderBy('title');
    }
    
    public function queryFilmsListWithAvailable(FilmFilterDto $dto, int $userId): Builder
    {
        return Film::with('language:id,name')
                    ->leftJoin('person.users_films', function(JoinClause $join) use ($userId) {
                        $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                            ->where('person.users_films.user_id', $userId);
                    })
                ->select('id', 'title', 'description', 'language_id')
                ->selectRaw('coalesce (person.users_films.user_id::bool, false) AS "isAvailable"')
                ->filter($dto)
                ->orderBy('title');
    }
    
    public function queryFilmsListWithActors(FilmFilterDto $dto): Builder
    {
        return Film::with('language:id,name')
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
                ->filter($dto)
                ->groupBy('dvd.films.id')
                ->orderBy('title');
    }
    
    public function queryAllRowNumbers(): Builder
    {
        return Film::select(
                'id',
                DB::raw('row_number() OVER(ORDER BY title) AS n')
            )
            ->orderBy('title');
    }
    
    public function queryActorsList(int $filmId): Builder
    {
        return Film::where('id', $filmId)
                            ->with([
                                'actors' => function (ContractsBuilder $query) {
                                    $query->select('id', 'first_name', 'last_name')
                                        ->orderBy('first_name')
                                        ->orderBy('last_name');
                                }
                            ])
                            ->select('id');
    }
}

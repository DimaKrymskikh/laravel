<?php

namespace App\Queries\Dvd\Films\FilmsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdminFilmsListForPageQueries extends BaseFilmsListForPageQueries
{
    protected function queryList(FilmFilterDto $dto, int|null $userId = null): Builder
    {
        return Film::select(
                        'dvd.films.id',
                        'dvd.films.title',
                        'dvd.films.description',
                        'dvd.films.release_year AS releaseYear',
                        'thesaurus.languages.name AS languageName',
                        DB::raw(<<<SQL
                                COALESCE(NULLIF(
                                    STRING_AGG(CONCAT(dvd.actors.first_name, ' ', dvd.actors.last_name), ', '), ' '
                                ), 'Актёры не добавлены') AS "actorsList"
                            SQL)
                    )
                ->leftJoin('dvd.films_actors', 'dvd.films_actors.film_id', '=', 'dvd.films.id')
                ->leftJoin('dvd.actors', 'dvd.actors.id', '=', 'dvd.films_actors.actor_id')
                ->leftJoin('thesaurus.languages', 'thesaurus.languages.id', '=', 'dvd.films.language_id')
                ->when($dto->languageName, function (Builder $query, string $name) {
                    $query->where('thesaurus.languages.name', 'ILIKE', "%$name%");
                })
                ->filter($dto)
                ->groupBy('dvd.films.id', 'thesaurus.languages.name')
                ->orderBy('dvd.films.title');
    }
}

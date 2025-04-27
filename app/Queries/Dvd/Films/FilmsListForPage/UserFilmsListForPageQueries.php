<?php

namespace App\Queries\Dvd\Films\FilmsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

final class UserFilmsListForPageQueries extends BaseFilmsListForPageQueries
{
    protected function queryList(FilmFilterDto $dto, int|null $userId = null): Builder
    {
        return Film::select(
                    'dvd.films.id',
                    'dvd.films.title',
                    'dvd.films.description',
                    'dvd.films.release_year AS releaseYear',
                    'thesaurus.languages.name AS languageName'
                )
                ->join('person.users_films', function(JoinClause $join) use ($userId) {
                    $join->on('person.users_films.film_id', '=', 'dvd.films.id')
                        ->where('person.users_films.user_id', $userId);
                })
                ->leftJoin('thesaurus.languages', 'thesaurus.languages.id', '=', 'dvd.films.language_id')
                ->when($dto->languageName, function (Builder $query, string $name) {
                    $query->where('thesaurus.languages.name', 'ILIKE', "%$name%");
                })
                ->filter($dto)
                ->orderBy('dvd.films.title');
    }
}

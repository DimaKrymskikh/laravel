<?php

namespace App\Queries\Dvd\Films\FilmsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use Illuminate\Database\Eloquent\Builder;

final class GuestFilmsListForPageQueries extends BaseFilmsListForPageQueries
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
                ->leftJoin('thesaurus.languages', 'thesaurus.languages.id', '=', 'dvd.films.language_id')
                ->when($dto->languageName, function (Builder $query, string $name) {
                    $query->where('thesaurus.languages.name', 'ILIKE', "%$name%");
                })
                ->filter($dto)
                ->orderBy('dvd.films.title');
    }
}

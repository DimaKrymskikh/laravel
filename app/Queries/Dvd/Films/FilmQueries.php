<?php

namespace App\Queries\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Dvd\Film;
use App\Providers\BindingInterfaces\QueriesProvider;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class FilmQueries implements FilmQueriesInterface
{
    public function exists(int $id): bool
    {
        return Film::where('id', $id)->exists();
    }
    
    public function getById(int $id): Film
    {
        return Film::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getRowNumbers(): Collection
    {
        return Film::select(
                'id',
                DB::raw('row_number() OVER(ORDER BY title) AS n')
            )
            ->orderBy('title')
            ->get();
    }
    
    public function count(FilmFilterDto $dto): int
    {
        return Film::filter($dto)->count();
    }
    
    public function getList(FilmFilterDto $dto): Collection
    {
        return Film::with('language:id,name')
                ->select('id', 'title', 'description', 'language_id', 'release_year')
                ->filter($dto)
                ->orderBy('title')
                ->limit(QueriesProvider::DEFAULT_LIMIT)
                ->get();
    }
    
    public function getByIdWithActors(int $id): Film
    {
        return Film::with([
                'language:id,name',
                'actors' => function (Builder $query) {
                    $query->select('id', 'first_name', 'last_name')
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->find($id);
    }
}

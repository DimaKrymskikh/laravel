<?php

namespace App\Repositories\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Film;
use App\Providers\BindingInterfaces\RepositoriesProvider;
use Illuminate\Contracts\Database\Eloquent\Builder as ContractsBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class FilmRepository implements FilmRepositoryInterface
{
    public function exists(int $filmId): bool
    {
        return Film::where('id', $filmId)->exists();
    }
    
    public function count(FilmFilterDto $dto): int
    {
        return Film::filter($dto)->count();
    }
    
    public function getById(int $filmId): Film
    {
        return Film::find($filmId);
    }
    
    public function getByIdWithActors(int $filmId): Film
    {
        return Film::with([
                'language:id,name',
                'actors' => function (ContractsBuilder $query) {
                    $query->select('id', 'first_name', 'last_name')
                        ->orderBy('first_name')
                        ->orderBy('last_name');
                }
            ])
            ->select('id', 'title', 'description', 'release_year', 'language_id')
            ->find($filmId);
    }
    
    public function getList(FilmFilterDto $dto): Collection
    {
        return $this->queryList($dto)->limit(RepositoriesProvider::DEFAULT_LIMIT)->get();
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
    
    private function queryList(FilmFilterDto $dto): Builder
    {
        return Film::with('language:id,name')
                ->select('id', 'title', 'description', 'language_id', 'release_year')
                ->filter($dto)
                ->orderBy('title');
    }
}

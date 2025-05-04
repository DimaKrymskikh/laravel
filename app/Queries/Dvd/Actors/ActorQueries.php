<?php

namespace App\Queries\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Models\Dvd\Actor;
use App\Providers\BindingInterfaces\QueriesProvider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class ActorQueries implements ActorQueriesInterface
{
    public function exists(int $id): bool
    {
        return Actor::where('id', $id)->exists();
    }
    
    public function getById(int $id): Actor
    {
        return Actor::find($id);
    }
    
    public function count(ActorFilterDto $dto): int
    {
        return Actor::filter($dto)->count();
    }
    
    public function getRowNumbers(): Collection
    {
        return Actor::select(
                'id',
                DB::raw('row_number() OVER(ORDER BY first_name, last_name) AS n')
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }
    
    public function getList(ActorFilterDto $dto): Collection
    {
        return Actor::select(
                    'id',
                    'first_name',
                    'last_name'
                )
                ->filter($dto)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(QueriesProvider::DEFAULT_LIMIT)
                ->get();
    }
}

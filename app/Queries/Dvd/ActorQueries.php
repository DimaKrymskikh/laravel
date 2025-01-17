<?php

namespace App\Queries\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Models\Dvd\Actor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ActorQueries
{
    public function getActorById(int $actorId): Actor
    {
        return Actor::find($actorId);
    }
    
    public function queryActorsList(ActorFilterDto $dto): Builder
    {
        return Actor::select(
                'id',
                'first_name',
                'last_name'
            )
            ->filter($dto)
            ->orderBy('first_name')
            ->orderBy('last_name');
    }
    
    public function queryAllRowNumbers(): Builder
    {
        return Actor::select(
                'id',
                DB::raw('row_number() OVER(ORDER BY first_name, last_name) AS n')
            )
            ->orderBy('first_name')
            ->orderBy('last_name');
    }
    
    public function getActorsCount(ActorFilterDto $dto): int
    {
        return Actor::filter($dto)->count();
    }
}

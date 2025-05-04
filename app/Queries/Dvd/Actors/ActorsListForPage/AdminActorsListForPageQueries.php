<?php

namespace App\Queries\Dvd\Actors\ActorsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Models\Dvd\Actor;
use Illuminate\Database\Eloquent\Builder;

final class AdminActorsListForPageQueries extends BaseActorsListForPageQueries
{
    protected function queryList(ActorFilterDto $dto): Builder
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
}

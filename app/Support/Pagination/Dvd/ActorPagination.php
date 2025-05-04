<?php

namespace App\Support\Pagination\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class ActorPagination
{
    public function paginate(Builder $query, PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'name' => $actorFilterDto->name
                ]);
    }
}

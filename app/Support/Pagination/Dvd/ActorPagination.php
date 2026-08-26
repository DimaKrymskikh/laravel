<?php

namespace App\Support\Pagination\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Pagination\PaginatorDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class ActorPagination
{
    /**
     * По строке запроса $query возвращает объект пагинации.
     * 
     * @param Builder $query
     * @param PaginatorDTO $paginatorDto
     * @param ActorFilterDto $actorFilterDto
     * @return LengthAwarePaginator
     */
    public function paginate(Builder $query, PaginatorDTO $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
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

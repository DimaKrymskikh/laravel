<?php

namespace App\Queries\Dvd\Actors\ActorsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Support\Pagination\Dvd\ActorPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseActorsListForPageQueries implements ActorsListForPageQueriesInterface
{
    public function __construct(
            private ActorPagination $pagination
    ) {
    }
    
    public function get(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        $query =  $this->queryList($actorFilterDto);
        
        return $this->pagination->paginate($query, $paginatorDto, $actorFilterDto);
    }
    
    abstract protected function queryList(ActorFilterDto $dto): Builder;
}

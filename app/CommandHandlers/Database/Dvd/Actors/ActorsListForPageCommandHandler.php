<?php

namespace App\CommandHandlers\Database\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Queries\Dvd\Actors\ActorsListForPage\ActorsListForPageQueriesInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class ActorsListForPageCommandHandler
{
    public function __construct(
            private ActorsListForPageQueriesInterface $queries
    ) {
    }
    
    public function handle(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        return $this->queries->get($paginatorDto, $actorFilterDto);
    }
}

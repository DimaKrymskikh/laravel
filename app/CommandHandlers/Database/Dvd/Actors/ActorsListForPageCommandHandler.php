<?php

namespace App\CommandHandlers\Database\Dvd\Actors;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Pagination\PaginatorDTO;
use App\Queries\Dvd\ActorsListForPage\ActorsListForPageQueriesInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class ActorsListForPageCommandHandler
{
    public function __construct(
            private ActorsListForPageQueriesInterface $queries
    ) {
    }
    
    public function handle(PaginatorDTO $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator
    {
        return $this->queries->get($paginatorDto, $actorFilterDto);
    }
}

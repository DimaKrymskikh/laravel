<?php

namespace App\Queries\Dvd\ActorsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Pagination\LengthAwarePaginator;

interface ActorsListForPageQueriesInterface 
{
    public function get(PaginatorDto $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator;
}

<?php

namespace App\Queries\Dvd\ActorsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Pagination\PaginatorDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface ActorsListForPageQueriesInterface 
{
    /**
     * Возвращает страницу пагинации
     * 
     * @param PaginatorDTO $paginatorDto Параметры пагинации
     * @param ActorFilterDto $actorFilterDto Параметры фильтра
     * @return LengthAwarePaginator
     */
    public function get(PaginatorDTO $paginatorDto, ActorFilterDto $actorFilterDto): LengthAwarePaginator;
}

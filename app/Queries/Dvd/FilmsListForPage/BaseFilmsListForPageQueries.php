<?php

namespace App\Queries\Dvd\FilmsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;
use App\Support\Pagination\Dvd\FilmPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseFilmsListForPageQueries implements FilmsListForPageQueriesInterface
{
    public function __construct(
            private FilmPagination $pagination,
    ) {
    }

    public function get(PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto, int|null $userId = null): LengthAwarePaginator
    {
        $query = $this->queryList($filmFilterDto, $userId);
        
        return $this->pagination->paginate($query, $paginatorDto, $filmFilterDto);
    }
    
    abstract protected function queryList(FilmFilterDto $dto, int|null $userId = null): Builder;
}

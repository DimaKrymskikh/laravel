<?php

namespace App\Queries\Dvd\Films;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Pagination\LengthAwarePaginator;

interface FilmsListForPageQueriesInterface
{
    public function get(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int|null $userId = null): LengthAwarePaginator;
}

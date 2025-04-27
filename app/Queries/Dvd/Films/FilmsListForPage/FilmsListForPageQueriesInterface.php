<?php

namespace App\Queries\Dvd\Films\FilmsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Pagination\LengthAwarePaginator;

interface FilmsListForPageQueriesInterface
{
    /**
     * Возвращает страницу пагинации
     * 
     * @param PaginatorDto $paginatorDto - параметры пагинации
     * @param FilmFilterDto $filmFilterDto - параметры фильтра
     * @param int|null $userId - id пользователя
     * @return LengthAwarePaginator
     */
    public function get(PaginatorDto $paginatorDto, FilmFilterDto $filmFilterDto, int|null $userId = null): LengthAwarePaginator;
}

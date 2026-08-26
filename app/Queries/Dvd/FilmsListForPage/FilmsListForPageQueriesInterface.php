<?php

namespace App\Queries\Dvd\FilmsListForPage;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Pagination\PaginatorDTO;
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
    public function get(PaginatorDTO $paginatorDto, FilmFilterDto $filmFilterDto, int|null $userId = null): LengthAwarePaginator;
}

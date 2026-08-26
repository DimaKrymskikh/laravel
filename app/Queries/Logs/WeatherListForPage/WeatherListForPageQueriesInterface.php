<?php

namespace App\Queries\Logs\WeatherListForPage;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Pagination\PaginatorDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface WeatherListForPageQueriesInterface
{
    /**
     * Возвращает страницу пагинации
     * 
     * @param PaginatorDTO $paginatorDto  Параметры пагинации
     * @param WeatherFilterDto $weatherFilterDto Параметры фильтра
     * @param int $cityId id города
     * @return LengthAwarePaginator
     */
    public function get(PaginatorDTO $paginatorDto, WeatherFilterDto $weatherFilterDto, int $cityId): LengthAwarePaginator;
}

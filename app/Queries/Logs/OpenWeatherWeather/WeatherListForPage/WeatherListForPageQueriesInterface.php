<?php

namespace App\Queries\Logs\OpenWeatherWeather\WeatherListForPage;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Pagination\LengthAwarePaginator;

interface WeatherListForPageQueriesInterface
{
    public function get(PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto, int $cityId): LengthAwarePaginator;
}

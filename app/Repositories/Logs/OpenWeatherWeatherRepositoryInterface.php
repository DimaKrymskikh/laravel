<?php

namespace App\Repositories\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Pagination\LengthAwarePaginator;

interface OpenWeatherWeatherRepositoryInterface
{
    public function getNumberOfWeatherLinesForLastMinute(): int;
    
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool;
    
    public function getByCityIdForPage(PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto, int $cityId): LengthAwarePaginator;
}

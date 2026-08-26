<?php

namespace App\Queries\Logs\WeatherListForPage;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Pagination\PaginatorDTO;
use App\Support\Pagination\Logs\WeatherPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseWeatherListForPageQueries implements WeatherListForPageQueriesInterface
{
    public function __construct(
            private WeatherPagination $pagination
    ) {
    }
    
    public function get(PaginatorDTO $paginatorDto, WeatherFilterDto $weatherFilterDto, int $cityId): LengthAwarePaginator
    {
        $query = $this->queryList($weatherFilterDto, $cityId);
        
        return $this->pagination->paginate($query, $paginatorDto, $weatherFilterDto);
    }

    abstract protected function queryList(WeatherFilterDto $dto, int $cityId): Builder;
}

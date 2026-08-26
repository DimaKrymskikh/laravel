<?php

namespace App\Support\Pagination\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Pagination\PaginatorDTO;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class WeatherPagination
{
    /**
     * По строке запроса $query возвращает объект пагинации.
     * 
     * @param Builder $query
     * @param PaginatorDTO $paginatorDto
     * @param WeatherFilterDto $weatherFilterDto
     * @return LengthAwarePaginator
     */
    public function paginate(Builder $query, PaginatorDTO $paginatorDto, WeatherFilterDto $weatherFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'datefrom' => $weatherFilterDto->datefrom->value,
                    'dateto' => $weatherFilterDto->dateto->value,
                ]);
    }
}

<?php

namespace App\Support\Pagination\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class WeatherPagination
{
    public function paginate(Builder $query, PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto): LengthAwarePaginator
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

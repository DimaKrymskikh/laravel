<?php

namespace App\Services\Database\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Thesaurus\City;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class OpenWeatherWeatherService
{
    public function __construct(
            private OpenWeatherWeatherQueries $openWeatherWeatherQueries,
            private TimezoneService $timezoneService,
    ) {
    }
    
    public function getWeatherListForPageByCity(PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto, City $city): LengthAwarePaginator
    {
        $query = $this->openWeatherWeatherQueries->queryWeatherList($weatherFilterDto, $city->id);
        
        $weatherList = $this->paginate($query, $paginatorDto, $weatherFilterDto);
        $this->timezoneService->setCityTimezoneForCollectionOfWeatherData($city, collect($weatherList->items()));
        
        return $weatherList;
    }
    
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

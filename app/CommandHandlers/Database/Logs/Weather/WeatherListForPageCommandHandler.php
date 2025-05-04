<?php

namespace App\CommandHandlers\Database\Logs\Weather;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Thesaurus\City;
use App\Queries\Logs\OpenWeatherWeather\WeatherListForPage\WeatherListForPageQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Pagination\LengthAwarePaginator;

final class WeatherListForPageCommandHandler
{
    public function __construct(
            private WeatherListForPageQueriesInterface $weatherListForPageQueries,
            private TimezoneService $timezoneService
    ) {
    }
    
    public function handle(PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto, City $city): LengthAwarePaginator
    {
        $weatherList = $this->weatherListForPageQueries->get($paginatorDto, $weatherFilterDto, $city->id);
        $this->timezoneService->setCityTimezoneForCollectionOfWeatherData($city, collect($weatherList->items()));
        
        return $weatherList;
    }
}

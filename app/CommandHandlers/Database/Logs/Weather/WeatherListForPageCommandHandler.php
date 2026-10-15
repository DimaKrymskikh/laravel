<?php

namespace App\CommandHandlers\Database\Logs\Weather;

use App\Contracts\Services\Database\Thesaurus\TimezoneServiceInterface;
use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Pagination\PaginatorDTO;
use App\Models\Thesaurus\City;
use App\Queries\Logs\WeatherListForPage\WeatherListForPageQueriesInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class WeatherListForPageCommandHandler
{
    public function __construct(
            private WeatherListForPageQueriesInterface $weatherListForPageQueries,
            private TimezoneServiceInterface $timezoneService
    ) {
    }
    
    public function handle(PaginatorDTO $paginatorDto, WeatherFilterDto $weatherFilterDto, City $city): LengthAwarePaginator
    {
        $weatherList = $this->weatherListForPageQueries->get($paginatorDto, $weatherFilterDto, $city->id);
        $this->timezoneService->setCityTimezoneForCollectionOfWeatherData($city, collect($weatherList->items()));
        
        return $weatherList;
    }
}

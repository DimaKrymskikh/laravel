<?php

namespace App\Services\Database\Logs;

use App\Exceptions\OpenWeatherException;
use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Models\Thesaurus\City;
use App\Repositories\Logs\OpenWeatherWeatherRepositoryInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Pagination\LengthAwarePaginator;

final class OpenWeatherWeatherService
{
    // Число запросов на сервер OpenWeather при бесплатном тарифе за одну минуту
    public const OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE = 60;
    // Период обновления погоды для города в минутах
    public const OPEN_WEATHER_CITY_UPDATE_PERIOD = 10;
    
    public function __construct(
            private OpenWeatherWeatherRepositoryInterface $openWeatherWeatherRepository,
            private TimezoneService $timezoneService,
    ) {
    }
    
    public function checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        if($this->openWeatherWeatherRepository->getNumberOfWeatherLinesForLastMinute() >= self::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) {
            throw new OpenWeatherException('Превышен лимит запросов на сервер OpenWeather. Подождите одну минуту.');
        }
    }
    
    public function checkTooEarlyToSubmitRequestForThisCity(int $cityId): void
    {
        if($this->openWeatherWeatherRepository->isTooEarlyToSubmitRequestForThisCity($cityId)) {
            throw new OpenWeatherException('На сервере OpenWeather данные о погоде в городе не обновились.');
        }
    }
    
    public function getWeatherListForPageByCity(PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto, City $city): LengthAwarePaginator
    {
        $weatherList = $this->openWeatherWeatherRepository->getByCityIdForPage($paginatorDto, $weatherFilterDto, $city->id);
        $this->timezoneService->setCityTimezoneForCollectionOfWeatherData($city, collect($weatherList->items()));
        
        return $weatherList;
    }
}

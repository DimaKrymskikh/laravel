<?php

namespace App\CommandHandlers\OpenWeather\Facades;

use App\Curl\OpenWeather\OpenWeatherRequests;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Modifiers\OpenWeather\Weather\WeatherModifiers;
use App\Modifiers\Thesaurus\Cities\CityModifiers;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueries;
use App\Queries\Person\Users\UserQueries;
use App\Queries\Thesaurus\Cities\CityQueries;
use App\Queries\Thesaurus\Timezones\TimezoneQueries;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\CityService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Support\Collections\Thesaurus\CityCollection;
use Illuminate\Http\Client\Response;

final class OpenWeatherFacade implements OpenWeatherFacadeInterface
{
    private OpenWeatherRequests $requests;
    private CityService $cityService;
    private WeatherService $weatherService;
    private OpenWeatherWeatherService $openWeatherWeatherService;

    
    public function __construct()
    {
        $this->requests = new OpenWeatherRequests();
        
        $cityModifiers = new CityModifiers();
        $cityQueries = new CityQueries();
        $this->cityService = new CityService($cityModifiers, $cityQueries);
        
        $weatherModifiers = new WeatherModifiers();
        $userQueries = new UserQueries();
        $timezoneQueries = new TimezoneQueries();
        $timezoneService = new TimezoneService($timezoneQueries);
        $this->weatherService = new WeatherService($weatherModifiers, $userQueries, $cityQueries, $timezoneService);
        
        $openWeatherWeatherQueries = new OpenWeatherWeatherQueries();
        $this->openWeatherWeatherService = new OpenWeatherWeatherService($openWeatherWeatherQueries);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getWeatherByCity(City $city): Response
    {
        return $this->requests->getWeatherByCity($city);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function findCityByOpenWeatherId(int $openWeatherId): City
    {
        return $this->cityService->findCityByOpenWeatherId($openWeatherId);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getAllCitiesList(): CityCollection
    {
        return $this->cityService->getAllCitiesList();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function updateOrCreate(WeatherDto $dto): Weather
    {
        return $this->weatherService->updateOrCreate($dto);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        $this->openWeatherWeatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function checkTooEarlyToSubmitRequestForThisCity(int $cityId): void
    {
        $this->openWeatherWeatherService->checkTooEarlyToSubmitRequestForThisCity($cityId);
    }
}

<?php

namespace App\Support\Facades\Services\OpenWeather;

use App\Curl\OpenWeather\OpenWeatherRequests;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Modifiers\OpenWeather\Weather\WeatherModifiers;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Queries\Person\Users\UserQueries;
use App\Queries\Thesaurus\Cities\CityQueries;
use App\Queries\Thesaurus\Timezones\TimezoneQueries;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;

/**
 * Содержит методы для работы с сервисом OpenWeather
 */
final class OpenWeatherFacade implements OpenWeatherFacadeInterface
{
    private OpenWeatherRequests $requests;
    private WeatherModifiers $weatherModifiers;
    private UserQueries $userQueries;
    private CityQueries $cityQueries;
    private OpenWeatherQueries $weatherQueries;
    private OpenWeatherWeatherQueries $openWeatherWeatherQueries;
    private TimezoneService $timezoneService;
    
    public function __construct()
    {
        $this->requests = new OpenWeatherRequests();
        $this->weatherModifiers = new WeatherModifiers();
        $this->userQueries = new UserQueries();
        $this->cityQueries = new CityQueries();
        $this->weatherQueries = new OpenWeatherQueries();
        $this->openWeatherWeatherQueries = new OpenWeatherWeatherQueries();
        
        $timezoneQueries = new TimezoneQueries();
        $this->timezoneService = new TimezoneService($timezoneQueries);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getWeatherFromOpenWeatherByCity(City $city): Response
    {
        return $this->requests->getWeatherByCity($city);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function updateOrCreate(WeatherDto $dto): void
    {
        $this->weatherModifiers->updateOrCreate($dto);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getWeatherByCityId(int $cityId): Weather
    {
        return $this->weatherQueries->getByCityId($cityId);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getUserById(int $userId): User
    {
        return $this->userQueries->getById($userId);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getCityById(int $cityId): City
    {
        return $this->cityQueries->getById($cityId);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getWeatherInCitiesForUser(User $user): Collection
    {
        return $this->cityQueries->getByUserWithWeather($user);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function setTimezoneOfCitiesForWeatherData(Collection $cities): void
    {
        $this->timezoneService->setTimezoneOfCitiesForWeatherData($cities);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getNumberOfWeatherLinesForLastMinute(): int
    {
        return $this->openWeatherWeatherQueries->getNumberOfWeatherLinesForLastMinute();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool
    {
        return $this->openWeatherWeatherQueries->isTooEarlyToSubmitRequestForThisCity($cityId);
    }
}

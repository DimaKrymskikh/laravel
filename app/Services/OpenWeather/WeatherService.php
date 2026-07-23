<?php

namespace App\Services\OpenWeather;

use App\Curl\OpenWeather\OpenWeatherRequests;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Events\RefreshCityWeather;
use App\Exceptions\OpenWeatherException;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Modifiers\OpenWeather\WeatherModifiers;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Queries\Person\UserQueries;
use App\Queries\Thesaurus\CityQueries;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Services\ServiceManagerInterface;
use App\ValueObjects\Broadcast\Weather\CurrentWeatherData;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;

/**
 * Бизнес-логика работы с сервисом OpenWeather
 */
final class WeatherService
{
    // Число запросов на сервер OpenWeather при бесплатном тарифе за одну минуту
    public const OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE = 60;
    // Период обновления погоды для города в минутах
    public const OPEN_WEATHER_CITY_UPDATE_PERIOD = 10;
    
    private OpenWeatherRequests $openWeatherRequests;
    private WeatherModifiers $weatherModifiers;
    private OpenWeatherWeatherQueries $openWeatherWeatherQueries;
    private OpenWeatherQueries $openWeatherQueries;
    private UserQueries $userQueries;
    private CityQueries $cityQueries;

    public function __construct(
            private ServiceManagerInterface $serviceManager,
            private TimezoneService $timezoneService,
    ) {
        $this->openWeatherRequests = $this->serviceManager->getCurlRequest(OpenWeatherRequests::class);
        $this->weatherModifiers = $this->serviceManager->getQueriesOrModifiers(WeatherModifiers::class);
        $this->openWeatherWeatherQueries = $this->serviceManager->getQueriesOrModifiers(OpenWeatherWeatherQueries::class);
        $this->openWeatherQueries = $this->serviceManager->getQueriesOrModifiers(OpenWeatherQueries::class);
        $this->userQueries = $this->serviceManager->getQueriesOrModifiers(UserQueries::class);
        $this->cityQueries = $this->serviceManager->getQueriesOrModifiers(CityQueries::class);
    }
    
    /**
     * Сохраняет погоду в таблице open_weather.weather
     * 
     * @param WeatherDto $dto
     * @return Weather
     */
    public function updateOrCreate(WeatherDto $dto): Weather
    {
        $this->weatherModifiers->updateOrCreate($dto);
        
        return $this->openWeatherQueries->getByCityId($dto->cityId);
    }
    
    public function getWeatherInCitiesForAuthUserByUserId(int $userId): Collection
    {
        $user = $this->userQueries->getById($userId);
        $cities = $this->cityQueries->getByUserWithWeather($user);
        
        // Устанавливаем в данных погоды часовой пояс города
        $this->timezoneService->setTimezoneOfCitiesForWeatherData($cities);
        
        return $cities;
    }
    
    /**
     * Проверяет, не превышен ли предел запросов (WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) за одну минуту
     * 
     * @return void
     * @throws OpenWeatherException
     */
    public function checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        if($this->openWeatherWeatherQueries->getNumberOfWeatherLinesForLastMinute() >= WeatherService::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) {
            throw new OpenWeatherException('Превышен лимит запросов на сервер OpenWeather. Подождите одну минуту.');
        }
    }
    
    /**
     * Проверяет, что для города $city имеются данные о погоде за последнии WeatherService::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * 
     * @param int $cityId
     * @return void
     * @throws OpenWeatherException
     */
    public function checkTooEarlyToSubmitRequestForThisCity(int $cityId): void
    {
        if($this->openWeatherWeatherQueries->isTooEarlyToSubmitRequestForThisCity($cityId)) {
            throw new OpenWeatherException('На сервере OpenWeather данные о погоде в городе не обновились.');
        }
    }
    
    /**
     * Отправляет http-запрос на сервер OpenWeather для города $city и возвращает ответ.
     * 
     * @param City $city
     * @return Response
     */
    public function sendRequest(City $city): Response
    {
        $this->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
        $this->checkTooEarlyToSubmitRequestForThisCity($city->id);
        
        return $this->openWeatherRequests->getWeatherByCity($city);
    }
    
    /**
     * Сохраняет ответ сервера OpenWeather в таблице 'open_weather.weather'.
     * 
     * @param Response $response
     * @param City $city
     * @return Weather Сохранённая запись в таблице 'open_weather.weather'.
     */
    public function saveResponse(Response $response, City $city): Weather
    {
        $dto = new WeatherDto($city->id, OpenWeatherObject::create($response->object()));
        
        return $this->updateOrCreate($dto);
    }
    
    /**
     * Обновляет погоду в городе с id = $dto->cityId.
     * 
     * @param UserCityDto $dto
     * @return void
     */
    public function refreshWeatherInCity(UserCityDto $dto, Dispatcher $dispatcher): void
    {
        $city = $this->cityQueries->getById($dto->cityId);
        $response = $this->sendRequest($city);
        
        if($response->status() !== 200) {
            return;
        }
        
        $weather = $this->saveResponse($response, $city);
        
        $dispatcher->dispatch(new RefreshCityWeather($dto->userId, CurrentWeatherData::create($weather, $city)));
    }
}

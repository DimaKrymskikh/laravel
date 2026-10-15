<?php

namespace App\Services\OpenWeather;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\Database\Thesaurus\TimezoneServiceInterface;
use App\Contracts\Services\OpenWeather\OpenWeatherInterface;
use App\Contracts\Services\ServiceManagerInterface;
use App\Curl\OpenWeather\OpenWeatherRequests;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Events\RefreshCityWeather;
use App\Exceptions\OpenWeatherException;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Modifiers\OpenWeather\WeatherModifiers;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Queries\OpenWeather\OpenWeatherQueries;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Support\Collections\Thesaurus\CityCollection;
use App\ValueObjects\Broadcast\Weather\CurrentWeatherData;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Client\Response;

/**
 * Бизнес-логика работы с сервисом OpenWeather
 */
final class WeatherService implements OpenWeatherInterface
{
    // Число запросов на сервер OpenWeather при бесплатном тарифе за одну минуту
    public const OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE = 60;
    // Период обновления погоды для города в минутах
    public const OPEN_WEATHER_CITY_UPDATE_PERIOD = 10;
    
    private readonly OpenWeatherRequests $openWeatherRequests;
    private readonly WeatherModifiers $weatherModifiers;
    private readonly OpenWeatherWeatherQueries $openWeatherWeatherQueries;
    private readonly OpenWeatherQueries $openWeatherQueries;

    public function __construct(
            private readonly ServiceManagerInterface $serviceManager,
            private readonly InfoAboutCitiesInterface $cityService,
            private readonly TimezoneServiceInterface $timezoneService,
    ) {
        $this->openWeatherRequests = $this->serviceManager->getCurlRequest(OpenWeatherRequests::class);
        $this->weatherModifiers = $this->serviceManager->getQueriesOrModifiers(WeatherModifiers::class);
        $this->openWeatherWeatherQueries = $this->serviceManager->getQueriesOrModifiers(OpenWeatherWeatherQueries::class);
        $this->openWeatherQueries = $this->serviceManager->getQueriesOrModifiers(OpenWeatherQueries::class);
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
    
    /**
     * Возвращает города пользователя с текущей погодой.
     * 
     * @param User|null $user
     * @return CityCollection
     * @throws \LogicException
     */
    public function getWeatherInCitiesForAuthUser(User|null $user): CityCollection
    {
        if(!$user) {
            throw new \LogicException('Пользователь не аутентифицирован.');
        }
        
        $cities = $this->cityService->getCitiesWithWeatherByUser($user);
        
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
        $city = $this->cityService->getCityById($dto->cityId);
        $response = $this->sendRequest($city);
        
        if($response->status() !== 200) {
            return;
        }
        
        $weather = $this->saveResponse($response, $city);
        
        $dispatcher->dispatch(new RefreshCityWeather($dto->userId, CurrentWeatherData::create($weather, $city)));
    }
}

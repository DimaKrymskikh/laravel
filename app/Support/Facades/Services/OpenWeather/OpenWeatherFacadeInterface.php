<?php

namespace App\Support\Facades\Services\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\User;
use App\Models\Thesaurus\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;

/**
 * Содержит методы для работы с сервисом OpenWeather.
 */
interface OpenWeatherFacadeInterface
{
    /**
     * Отправляет http-запрос на сервер OpenWeather для города $city.
     * 
     * @param City $city
     * @return Response
     */
    public function getWeatherFromOpenWeatherByCity(City $city): Response;
    
    /**
     * Сохраняет погоду в таблице 'open_weather.weather'
     * 
     * @param WeatherDto $dto
     * @return void
     */
    public function updateOrCreate(WeatherDto $dto): void;
    
    /**
     * Извлекает погоду по id-города из таблицы 'open_weather.weather'.
     * 
     * @param int $cityId
     * @return Weather
     */
    public function getWeatherByCityId(int $cityId): Weather;
    
    /**
     * Получить пользователя из таблицы 'person.users' по его id.
     * 
     * @param int $userId
     * @return User
     */
    public function getUserById(int $userId): User;
    
    /**
     * Получить из таблицы 'thesaurus.cities' город по первичному ключу id.
     * 
     * @param int $cityId Первичный ключ id.
     * @return City
     */
    public function getCityById(int $cityId): City;
    
    /**
     * Возвращает города пользователя $user с текущей погодой (таблица 'open_weather.weather').
     * 
     * @param User $user
     * @return Collection
     */
    public function getWeatherInCitiesForUser(User $user): Collection;
    
    /**
     * Для каждого города коллекции устанавливает у данных погоды временной пояс города.
     * Коллекция $cities должна быть получена жадной загрузкой, чтобы не было запросов в цикле.
     * 
     * @param Collection $cities
     * @return void
     */
    public function setTimezoneOfCitiesForWeatherData(Collection $cities): void;
    
    /**
     * Возвращает число строк с данными о погоде за последнюю минуту.
     * 
     * @return int
     */
    public function getNumberOfWeatherLinesForLastMinute(): int;
    
    /**
     * Определяет, имеются ли данные о погоде для города за последнии WeatherService::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * 
     * @param int $cityId id-города.
     * @return bool
     */
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool;
}

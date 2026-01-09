<?php

namespace App\CommandHandlers\OpenWeather\Facades;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Support\Collections\Thesaurus\CityCollection;
use Illuminate\Http\Client\Response;

interface OpenWeatherFacadeInterface 
{
    /**
     * Отправляет http-запрос на сервер OpenWeather для одного города. 
     * 
     * @param City $city
     * @return Response
     */
    public function getWeatherByCity(City $city): Response;
    
    /**
     * Находит и возвращает город по полю thesaurus.cities.open_weather_id
     * 
     * @param int $openWeatherId id-города в сервисе OpenWeather
     * @return City
     */
    public function findCityByOpenWeatherId(int $openWeatherId): City;
    
    /**
     * Получить все ряды таблицы 'thesaurus.cities'
     * 
     * @return CityCollection
     */
    public function getAllCitiesList(): CityCollection;
    
    /**
     * Сохраняет погоду в таблице open_weather.weather
     * 
     * @param WeatherDto $dto
     * @return Weather
     */
    public function updateOrCreate(WeatherDto $dto): Weather;
    
    /**
     * Проверяет, не превышен ли предел запросов (self::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) за одну минуту
     * 
     * @return void
     */
    public function checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void;
    
    /**
     * Проверяет, что для города $city имеются данные о погоде за последнии self::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * 
     * @param int $cityId
     * @return void
     */
    public function checkTooEarlyToSubmitRequestForThisCity(int $cityId): void;
}

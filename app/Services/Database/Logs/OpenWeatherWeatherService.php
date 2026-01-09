<?php

namespace App\Services\Database\Logs;

use App\Exceptions\OpenWeatherException;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueriesInterface;

final class OpenWeatherWeatherService
{
    // Число запросов на сервер OpenWeather при бесплатном тарифе за одну минуту
    public const OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE = 60;
    // Период обновления погоды для города в минутах
    public const OPEN_WEATHER_CITY_UPDATE_PERIOD = 10;
    
    public function __construct(
            private OpenWeatherWeatherQueriesInterface $openWeatherWeatherQueries,
    ) {
    }
    
    /**
     * Проверяет, не превышен ли предел запросов (self::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) за одну минуту
     * 
     * @return void
     * @throws OpenWeatherException
     */
    public function checkNumberOfWeatherLinesForLastMinuteLessBaseValue(): void
    {
        if($this->openWeatherWeatherQueries->getNumberOfWeatherLinesForLastMinute() >= self::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) {
            throw new OpenWeatherException('Превышен лимит запросов на сервер OpenWeather. Подождите одну минуту.');
        }
    }
    
    /**
     * Проверяет, что для города $city имеются данные о погоде за последнии self::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
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
}

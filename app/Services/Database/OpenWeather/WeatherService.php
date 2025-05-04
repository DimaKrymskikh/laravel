<?php

namespace App\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Modifiers\OpenWeather\Weather\WeatherModifiersInterface;
use App\Queries\Person\Users\UserQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Database\Eloquent\Collection;

final class WeatherService
{
    public function __construct(
            private WeatherModifiersInterface $weatherModifiers,
            private UserQueriesInterface $userQueries,
            private CityQueriesInterface $cityQueries,
            private TimezoneService $timezoneService,
    ) {
    }
    
    public function updateOrCreate(WeatherDto $dto): Weather
    {
        $weather = new Weather();
        
        $this->weatherModifiers->save($weather, $dto);
        
        return $weather;
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
     * Возвращает последние данные о погоде для города $city.
     * Данные берутся из таблицы open_weather.weather.
     * 
     * @param int $cityId
     * @return Weather
     */
    public function getLatestWeatherForOneCityByCityId(int $cityId): Weather
    {
        $city = $this->cityQueries->getById($cityId);
        
        $this->timezoneService->setCityTimezoneForWeatherData($city, $city->weather);
        
        return $city->weather;
    }
}

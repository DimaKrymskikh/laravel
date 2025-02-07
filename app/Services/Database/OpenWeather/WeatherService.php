<?php

namespace App\Services\Database\OpenWeather;

use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Models\OpenWeather\Weather;
use App\Repositories\OpenWeather\WeatherRepositoryInterface;
use App\Repositories\Person\UserRepositoryInterface;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Database\Eloquent\Collection;

final class WeatherService
{
    public function __construct(
            private WeatherRepositoryInterface $weatherRepository,
            private UserRepositoryInterface $userRepository,
            private CityRepositoryInterface $cityRepository,
            private TimezoneService $timezoneService,
    ) {
    }
    
    public function updateOrCreate(WeatherDto $dto): Weather
    {
        $weather = new Weather();
        
        $this->weatherRepository->save($weather, $dto);
        
        return $weather;
    }
    
    public function getWeatherInCitiesForAuthUserByUserId(int $userId): Collection
    {
        $user = $this->userRepository->getById($userId);
        $cities = $this->cityRepository->getByUserWithWeather($user);
        
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
        $city = $this->cityRepository->getById($cityId);
        
        $this->timezoneService->setCityTimezoneForWeatherData($city, $city->weather);
        
        return $city->weather;
    }
}

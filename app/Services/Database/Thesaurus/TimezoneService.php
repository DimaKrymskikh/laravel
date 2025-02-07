<?php

namespace App\Services\Database\Thesaurus;

use App\Models\Logs\OpenWeatherWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Repositories\Thesaurus\TimezoneRepositoryInterface;
use App\Services\CarbonService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class TimezoneService
{
    public function __construct(
            private TimezoneRepositoryInterface $timezoneRepository
    ) {
    }
    
    public function getAllTimezonesList(string $name): Collection
    {
        return $this->timezoneRepository->getList($name);
    }
    
    /**
     * Возвращает временной пояс города
     * 
     * @param City $city
     * @return string
     */
    public function getTimezoneByCity(City $city): string
    {
        // Если для города не задан временной пояс, берём дефолтный
        return $city->timezone_id ? $city->timezone->name : CarbonService::DEFAULT_TIMEZONE_NAME;
    }
    
    /**
     * Устанавливает в данных погоды временной пояс города.
     * (В базе время создания строки с данными погоды имеет часовой пояс приложения)
     * 
     * @param City $city
     * @param OpenWeatherWeather|Weather $weather
     * @return void
     */
    public function setCityTimezoneForWeatherData(City $city, OpenWeatherWeather|Weather $weather): void
    {
        $tzName = $this->getTimezoneByCity($city);
        $weather->created_at = CarbonService::setNewTimezone($weather->created_at, $tzName);
    }
    
    /**
     * Устанавливает в данных погоды (коллекция погоды) временной пояс города.
     * 
     * @param City $city
     * @param SupportCollection $weatherCollection
     * @return void
     */
    public function setCityTimezoneForCollectionOfWeatherData(City $city, SupportCollection $weatherCollection): void
    {
        $tzName = $this->getTimezoneByCity($city);
        foreach($weatherCollection as $weather) {
            $weather->created_at = CarbonService::setNewTimezone($weather->created_at, $tzName);
        }
    }
    
    /**
     * Для каждого города коллекции устанавливает у данных погоды временной пояс города.
     * Коллекция $cities должна быть получена жадной загрузкой, чтобы не было запросов в цикле.
     * 
     * @param Collection $cities
     * @return void
     */
    public function setTimezoneOfCitiesForWeatherData(Collection $cities): void
    {
        foreach($cities as $city) {
            // Пропускаем город, если он не связан с таблицей open_weather.weather
            // Например, для города ещё не получены данные о погоде
            if(!$city->weather) {
                continue;
            }
            $this->setCityTimezoneForWeatherData($city, $city->weather);
        }
    }
}

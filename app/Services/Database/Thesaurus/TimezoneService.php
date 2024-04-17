<?php

namespace App\Services\Database\Thesaurus;

use App\Models\Logs\OpenWeatherWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Services\CarbonService;
use Illuminate\Support\Collection;

final class TimezoneService
{
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
     * @param Collection $weatherCollection
     * @return void
     */
    public function setCityTimezoneForCollectionOfWeatherData(City $city, Collection $weatherCollection): void
    {
        // Использование setCityTimezoneForWeatherData может создать запрос в цикле,
        // потому что в getTimezoneByCity будут выполняться запросы при ленивой загрузке $city.
        $tzName = $this->getTimezoneByCity($city);
        foreach($weatherCollection as $weather) {
            $weather->created_at = CarbonService::setNewTimezone($weather->created_at, $tzName);
        }
    }
}

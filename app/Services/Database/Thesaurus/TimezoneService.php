<?php

namespace App\Services\Database\Thesaurus;

use App\Contracts\Services\Database\Thesaurus\TimezoneServiceInterface;
use App\Contracts\Services\ServiceManagerInterface;
use App\Models\Logs\OpenWeatherWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\TimezoneQueries;
use App\Services\Carbon\CarbonService;
use App\Support\Collections\Thesaurus\TimezoneCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class TimezoneService implements TimezoneServiceInterface
{
    private TimezoneQueries $timezoneQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager
    ) {
        $this->timezoneQueries = $this->serviceManager->getQueriesOrModifiers(TimezoneQueries::class);
    }
    
    public function getAllTimezonesList(string $name): TimezoneCollection
    {
        return $this->timezoneQueries->getList($name);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setCityTimezoneForWeatherData(City $city, OpenWeatherWeather|Weather $weather): void
    {
        $tzName = $city->getTimezoneName();
        $weather->created_at = CarbonService::setNewTimezone($weather->created_at, $tzName);
    }
    
    /**
     * {@inheritDoc}
     */
    public function setCityTimezoneForCollectionOfWeatherData(City $city, SupportCollection $weatherCollection): void
    {
        $tzName = $city->getTimezoneName();
        foreach($weatherCollection as $weather) {
            $weather->created_at = CarbonService::setNewTimezone($weather->created_at, $tzName);
        }
    }
    
    /**
     * {@inheritDoc}
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

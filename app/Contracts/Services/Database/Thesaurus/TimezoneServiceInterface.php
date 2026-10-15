<?php

namespace App\Contracts\Services\Database\Thesaurus;

use App\Models\Logs\OpenWeatherWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Support\Collections\Thesaurus\TimezoneCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface TimezoneServiceInterface
{
    public function getAllTimezonesList(string $name): TimezoneCollection;
    
    /**
     * Устанавливает в данных погоды временной пояс города.
     * (В базе время создания строки с данными погоды имеет часовой пояс приложения)
     * 
     * @param City $city
     * @param OpenWeatherWeather|Weather $weather
     * @return void
     */
    public function setCityTimezoneForWeatherData(City $city, OpenWeatherWeather|Weather $weather): void;
    
    /**
     * Устанавливает в данных погоды (коллекция погоды) временной пояс города.
     * 
     * @param City $city
     * @param SupportCollection $weatherCollection
     * @return void
     */
    public function setCityTimezoneForCollectionOfWeatherData(City $city, SupportCollection $weatherCollection): void;
    
    /**
     * Для каждого города коллекции устанавливает у данных погоды временной пояс города.
     * Коллекция $cities должна быть получена жадной загрузкой, чтобы не было запросов в цикле.
     * 
     * @param Collection $cities
     * @return void
     */
    public function setTimezoneOfCitiesForWeatherData(Collection $cities): void;
}

<?php

namespace App\Services\Database\Thesaurus;

use App\Models\Thesaurus\City;
use App\Services\CarbonService;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CityService
{
    public function __construct(
        private TimezoneService $timezoneService,
    )
    {}
    
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
            $this->setCityTimezoneForWeatherData($city);
        }
    }
    
    /**
     * Устанавливает временной пояс города для данных погоды
     * 
     * @param City $city
     * @return void
     */
    public function setCityTimezoneForWeatherData(City $city): void
    {
            $tzName = $this->timezoneService->getTimezoneByCity($city);
            $city->weather->created_at = CarbonService::setNewTimezone($city->weather->created_at, $tzName);
    }
    
    /**
     * Находит и возвращает город по полю thesaurus.cities.open_weather_id
     * 
     * @param type $openWeatherId
     * @return City
     */
    public function findCityByOpenWeatherId($openWeatherId): City
    {
        $city = City::where('open_weather_id', $openWeatherId)->first();
        
        return $city ?? throw ValidationException::withMessages([
                'message' => trans('city.openWeatherId.exist', ['openWeatherId' => $openWeatherId])
            ]);
    }
}

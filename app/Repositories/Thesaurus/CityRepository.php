<?php

namespace App\Repositories\Thesaurus;

use App\Models\Thesaurus\City;
use App\Services\Database\Thesaurus\CityService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class CityRepository
{
    private CityService $cityService;
    
    public function __construct()
    {
        $this->cityService = new CityService();
    }
    
    /**
     * Возвращает список городов с текущей погодой для залогиненного пользователя.
     * 
     * @param Request $request
     * @return Collection
     */
    public function getWeatherForCitiesOfAuth(Request $request): Collection
    {
        $cities = City::with([
            'weather:city_id,weather_description,main_temp,main_feels_like,main_pressure,main_humidity,visibility,wind_speed,wind_deg,clouds_all,created_at',
            'timezone:id,name'
        ])->select('id', 'name', 'open_weather_id', 'timezone_id')
            ->join('person.users_cities', function(JoinClause $join) use ($request) {
                $join->on('person.users_cities.city_id', 'thesaurus.cities.id')
                    ->where('person.users_cities.user_id', $request->user()->id);
            })
            ->orderBy('name')
            ->get();
        
        // Устанавливаем в данных погоды часовой пояс города
        $this->cityService->setTimezoneOfCitiesForWeatherData($cities);
       
        return $cities;
    }
}

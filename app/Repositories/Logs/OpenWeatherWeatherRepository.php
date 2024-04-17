<?php

namespace App\Repositories\Logs;

use App\Models\Logs\OpenWeatherWeather;
use App\Models\Thesaurus\City;
use App\Services\Database\Thesaurus\TimezoneService;
use App\Support\Pagination\Paginator;
use App\Support\Pagination\RequestGuard;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class OpenWeatherWeatherRepository
{
    use Paginator;
    
    public const ADDITIONAL_PARAMS_IN_URL = [];
    
    private RequestGuard $guard;
    
    public function __construct(
        private TimezoneService $timezoneService,
    )
    {
        $this->guard = new RequestGuard(self::ADDITIONAL_PARAMS_IN_URL);
    }
    
    public function getWeatherPageByCity(Request $request, City $city): LengthAwarePaginator
    {
        $query = OpenWeatherWeather::select(
                        'city_id',
                        'weather_description',
                        'main_temp',
                        'main_feels_like',
                        'main_pressure',
                        'main_humidity',
                        'visibility',
                        'wind_speed',
                        'wind_deg',
                        'clouds_all',
                        'created_at'
                    )
                ->where('city_id', $city->id)
                ->orderBy('created_at', 'desc');
        
        $weatherList = $this->setPagination($query, $request, $this->guard);
        
        $this->timezoneService->setCityTimezoneForCollectionOfWeatherData($city, collect($weatherList->items()));
        
        return $weatherList;
    }
}

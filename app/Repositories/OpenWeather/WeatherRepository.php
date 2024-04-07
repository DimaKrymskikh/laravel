<?php

namespace App\Repositories\OpenWeather;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Models\Logs\OpenWeatherWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Services\CarbonService;
use Illuminate\Support\Facades\DB;

class WeatherRepository
{
    /**
     * Возвращает последние данные о погоде для города $city.
     * Данные берутся из таблицы open_weather.weather.
     * 
     * @param City $city
     * @return Weather
     */
    public function getLatestWeatherForOneCity(City $city): Weather
    {
        $weather = Weather::select(
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
                ->first();
        
        // Время получения данных о погоде преобразуем в фактическое время в городе $city
        $tzName = $city->timezone_id ? $city->timezone->name : TimezoneInterface::DEFAULT_TIMEZONE_NAME;
        $weather->created_at = CarbonService::setNewTimezone($weather->created_at, $tzName);
        
        return $weather;
    }
    
    /**
     * Возвращает число строк с данными о погоде за последнюю минут.
     * Данные берутся из таблицы logs.open_weather__weather.
     * 
     * @return int
     */
    public function getNumberOfWeatherLinesForLastMinute(): int
    {
        return OpenWeatherWeather::selectRaw('count(*) AS n')
                ->whereRaw("created_at > now() - interval '1 minute'")
                ->first()->n;
    }
    
    /**
     * Определяет, имеются ли данные о погоде для города $city за последнии 
     * GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * Данные берутся из таблицы logs.open_weather__weather.
     * 
     * @param City $city
     * @return bool
     */
    public function isTooEarlyToSubmitRequestForThisCity(City $city): bool
    {
        $period = GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_CITY_UPDATE_PERIOD;
        
        return DB::scalar(<<<SQL
                SELECT EXISTS (
                    SELECT FROM logs.open_weather__weather w 
                    WHERE city_id = $city->id
                        AND created_at > now() - interval '$period minute'
                )
            SQL); 
    }
}

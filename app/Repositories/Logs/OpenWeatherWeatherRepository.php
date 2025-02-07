<?php

namespace App\Repositories\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\DataTransferObjects\Pagination\PaginatorDto;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use App\Models\Logs\OpenWeatherWeather;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class OpenWeatherWeatherRepository implements OpenWeatherWeatherRepositoryInterface
{
    /**
     * Возвращает число строк с данными о погоде за последнюю минуту.
     * 
     * @return int
     */
    public function getNumberOfWeatherLinesForLastMinute(): int
    {
        return OpenWeatherWeather::whereRaw("created_at > now() - interval '1 minute'")->count();
    }
    
    /**
     * Определяет, имеются ли данные о погоде для города $city за последнии 
     * GetWeatherFromOpenWeatherCommandHandler::OPEN_WEATHER_CITY_UPDATE_PERIOD минут.
     * 
     * @param int $cityId
     * @return bool
     */
    public function isTooEarlyToSubmitRequestForThisCity(int $cityId): bool
    {
        $period = OpenWeatherWeatherService::OPEN_WEATHER_CITY_UPDATE_PERIOD;
        
        return OpenWeatherWeather::where('city_id', $cityId)
                ->whereRaw("created_at > now() - interval '$period minute'")
                ->exists();
    }
    
    public function getByCityIdForPage(PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto, int $cityId): LengthAwarePaginator
    {
        $query = $this->queryWeatherList($weatherFilterDto, $cityId);
        
        return $this->paginate($query, $paginatorDto, $weatherFilterDto);
    }
    
    private function queryWeatherList(WeatherFilterDto $dto, int $cityId): Builder
    {
        return OpenWeatherWeather::select(
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
                    ->where('city_id', $cityId)
                    ->filter($dto)
                    ->orderBy('created_at', 'desc');
    }
    
    private function paginate(Builder $query, PaginatorDto $paginatorDto, WeatherFilterDto $weatherFilterDto): LengthAwarePaginator
    {
        $perPage = $paginatorDto->perPage->value;
                
        return $query
                ->paginate($perPage)
                ->appends([
                    'number' => $perPage,
                    'datefrom' => $weatherFilterDto->datefrom->value,
                    'dateto' => $weatherFilterDto->dateto->value,
                ]);
    }
}

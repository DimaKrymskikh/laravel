<?php

namespace App\ValueObjects\Broadcast\Weather;

use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Services\Carbon\Enums\DateFormat;
use App\Services\Carbon\CarbonService;

/**
 * Класс хранит текущую погоду в std-объекте.
 * Полезно использовать в событиях с очередями.
 */
final readonly class CurrentWeatherData
{
    public object $data;
    
    private function __construct(Weather $weather, City $city)
    {
        $tzName = $city->getTimezonName();
        
        $createdAt = CarbonService::setNewTimezoneInString($weather->created_at, 'UTC', $tzName, DateFormat::Full);
        
        $this->data = (object) [
            'id' => $weather->id,
            'city_id' => $weather->city_id,
            'weather_description' => $weather->weather_description,
            'main_temp' => $weather->main_temp,
            'main_feels_like' => $weather->main_feels_like,
            'main_pressure' => $weather->main_pressure,
            'main_humidity' => $weather->main_humidity,
            'visibility' => $weather->visibility,
            'wind_speed' => $weather->wind_speed,
            'wind_deg' => $weather->wind_deg,
            'clouds_all' => $weather->clouds_all,
            'created_at' => $createdAt
        ];
    }
    
    public static function create(Weather $weather, City $city): self
    {
        return new self($weather, $city);
    }
}

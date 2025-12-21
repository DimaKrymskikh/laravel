<?php

namespace App\Models\OpenWeather;

use App\Models\Thesaurus\City;
use App\Support\Collections\OpenWeather\WeatherCollection;
use Database\Factories\OpenWeather\WeatherFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Текущая погода.
 * 
 * @property int $id Первичный ключ таблицы 'open_weather.weather'.
 * @property int $city_id  id города (ссылка на thesaurus.cities.id).
 * @property string $weather_description Описание погодных условий.
 * @property float $main_temp Температура, C.
 * @property float $main_feels_like Этот температурный параметр определяет человеческое восприятие погоды, C.
 * @property int $main_pressure Атмосферное давление, hPa.
 * @property int $main_humidity Влажность, %.
 * @property int $visibility Видимость, m.
 * @property float $wind_speed Скорость ветра, m/s.
 * @property int $wind_deg Направление ветра, градусы (метеорологические).
 * @property int $clouds_all Облачность, %.
 * @property string $created_at
 */
class Weather extends Model
{
    use HasFactory;
    
    protected $table = 'open_weather.weather';
    
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'immutable_datetime:H:i:s d.m.Y',
    ];
    
    // Для всех полей таблицы разрешено массовое назначение
    protected $guarded = [];
    
    public function newCollection(array $models = []): WeatherCollection
    {
        return new WeatherCollection($models);
    }
    
    protected static function newFactory(): Factory
    {
        return WeatherFactory::new();
    }
    
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}

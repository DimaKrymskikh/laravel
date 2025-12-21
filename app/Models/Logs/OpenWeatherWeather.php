<?php

namespace App\Models\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use App\Support\Collections\Logs\OpenWeatherWeatherCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * История погоды.
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
class OpenWeatherWeather extends Model
{
    use HasFactory;
    
    protected $table = 'logs.open_weather__weather';
    
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'immutable_datetime:H:i:s d.m.Y',
    ];
    
    public function newCollection(array $models = []): OpenWeatherWeatherCollection
    {
        return new OpenWeatherWeatherCollection($models);
    }
    
    public function scopeFilter(Builder $query, WeatherFilterDto $dto): Builder
    {
        return $query
            ->when($dto->datefrom->value, function (Builder $query, string $datefrom) {
                $query->whereRaw("created_at >= to_date(?, 'DD.MM.YYYY')", $datefrom);
            })
            ->when($dto->dateto->value, function (Builder $query, string $dateto) {
                $query->whereRaw("created_at < to_date(?, 'DD.MM.YYYY') + '1 day'::interval", $dateto);
            });
    }
}

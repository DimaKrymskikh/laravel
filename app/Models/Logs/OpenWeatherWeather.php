<?php

namespace App\Models\Logs;

use App\DataTransferObjects\Database\OpenWeather\Filters\WeatherFilterDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenWeatherWeather extends Model
{
    use HasFactory;
    
    protected $table = 'logs.open_weather__weather';
    
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'immutable_datetime:H:i:s d.m.Y',
    ];
    
    public function scopeFilter(Builder $query, WeatherFilterDto $dto): Builder
    {
        return $query
            ->when($dto->datefrom->value, function (Builder $query, string $datefrom) {
                $query->whereRaw("created_at > to_date(?, 'DD.MM.YYYY')", $datefrom);
            })
            ->when($dto->dateto->value, function (Builder $query, string $dateto) {
                $query->whereRaw("created_at < to_date(?, 'DD.MM.YYYY') + '1 day'::interval", $dateto);
            });
    }
}

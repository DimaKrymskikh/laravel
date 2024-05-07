<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OpenWeatherWeather extends Model
{
    use HasFactory;
    
    protected $table = 'logs.open_weather__weather';
    
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'immutable_datetime:H:i:s d.m.Y',
    ];
    
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->datefrom, function (Builder $query, string $datefrom) {
                $query->whereRaw("created_at > to_date(?, 'DD.MM.YYYY')", $datefrom);
            })
            ->when($request->dateto, function (Builder $query, string $dateto) {
                $query->whereRaw("created_at < to_date(?, 'DD.MM.YYYY') + '1 day'::interval", $dateto);
            });
    }
}

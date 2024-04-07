<?php

namespace App\Models\Logs;

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
}

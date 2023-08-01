<?php

namespace App\Models\OpenWeather;

use Database\Factories\OpenWeather\WeatherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    use HasFactory;
    
    protected $table = 'open_weather.weather';
    
    public $timestamps = false;
    
    protected static function newFactory(): Factory
    {
        return WeatherFactory::new();
    }
}

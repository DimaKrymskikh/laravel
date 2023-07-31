<?php

namespace App\Models\OpenWeather;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    use HasFactory;
    
    protected $table = 'open_weather.weather';
    
    public $timestamps = false;
}

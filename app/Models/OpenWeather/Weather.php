<?php

namespace App\Models\OpenWeather;

use App\Models\Thesaurus\City;
use Database\Factories\OpenWeather\WeatherFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    use HasFactory;
    
    protected $table = 'open_weather.weather';
    
    public $timestamps = false;
    
    protected $casts = [
        'created_at' => 'immutable_datetime:H:i:s d.m.Y',
    ];
    
    protected static function newFactory(): Factory
    {
        return WeatherFactory::new();
    }
    
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}

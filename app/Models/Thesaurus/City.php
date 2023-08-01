<?php

namespace App\Models\Thesaurus;

use App\Models\OpenWeather\Weather;
use Database\Factories\Thesaurus\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;
    
    protected $table = 'thesaurus.cities';
    
    public $timestamps = false;
    
    protected static function newFactory(): Factory
    {
        return CityFactory::new();
    }
    
    public function weather(): HasMany
    {
        return $this->hasMany(Weather::class);
    }
}

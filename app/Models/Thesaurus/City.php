<?php

namespace App\Models\Thesaurus;

use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\Timezone;
use App\Models\User;
use Database\Factories\Thesaurus\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    
    /**
     * Используется для получения последних данных о погоде
     * 
     * @return HasOne
     */
    public function weatherFirst(): HasOne
    {
        return $this->hasOne(Weather::class);
    }
    
    public function timezone(): BelongsTo
    {
        return $this->BelongsTo(Timezone::class);
    }
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'person.users_cities', 'city_id', 'user_id');
    }
}

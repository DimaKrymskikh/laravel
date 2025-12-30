<?php

namespace App\Models\Thesaurus;

use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\Timezone;
use App\Models\User;
use App\Support\Collections\Thesaurus\CityCollection;
use Database\Factories\Thesaurus\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Города (таблица 'thesaurus.cities')
 * 
 * @property int $id Первичный ключ таблицы 'thesaurus.cities'.
 * @property string $name Название города.
 * @property int $open_weather_id ID города в службе OpenWeather.
 * @property int $timezone_id Часовой пояс (ссылка на thesaurus.timezones.id).
 * @property string $created_at
 * @property string $updated_at
 */
class City extends Model
{
    use HasFactory;
    
    protected $table = 'thesaurus.cities';
    
    public $timestamps = false;
    
    public function newCollection(array $models = []): CityCollection
    {
        return new CityCollection($models);
    }
    
    protected static function newFactory(): Factory
    {
        return CityFactory::new();
    }
    
    public function weather(): HasOne
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

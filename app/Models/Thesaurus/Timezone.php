<?php

namespace App\Models\Thesaurus;

use App\Support\Collections\Thesaurus\TimezoneCollection;
use Database\Factories\Thesaurus\TimezoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * Языки народов (таблица 'thesaurus.timezones')
 * 
 * @property int $id Первичный ключ таблицы 'thesaurus.timezones'.
 * @property string $name Часовой пояс.
 */
class Timezone extends Model
{
    use HasFactory;
    
    protected $table = 'thesaurus.timezones';
    
    public $timestamps = false;
    
    public function newCollection(array $models = []): TimezoneCollection
    {
        return new TimezoneCollection($models);
    }
    
    protected static function newFactory(): Factory
    {
        return TimezoneFactory::new();
    }
}

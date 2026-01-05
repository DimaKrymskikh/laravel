<?php

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Модель таблицы person.users_cities, которая связывает таблицы person.users и thesaurus.cities
 * 
 * @property int user_id Ссылка на person.users.id.
 * @property int city_id Ссылка на thesaurus.cities.id.
 * @property string $created_at
 */
class UserCity extends Model
{
    use HasFactory;
    
    protected $table = 'person.users_cities';
    
    // При сохранении данных не передаются поля created_at и updated_at
    public $timestamps = false;
    
    protected $primaryKey = 'user_id';
}

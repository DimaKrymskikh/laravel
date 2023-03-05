<?php

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFilm extends Model
{
    use HasFactory;
    
    protected $table = 'person.users_films';
    
    // При сохранении данных не передаются поля created_at и updated_at
    public $timestamps = false;
    
    // Метод save() будет возвращать user_id
    protected $primaryKey = 'user_id';
}

<?php

namespace App\Models\Person;

use Database\Factories\Person\UserFilmFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class UserFilm extends Model
{
    use HasFactory;
    
    protected $table = 'person.users_films';
    
    // При сохранении данных не передаются поля created_at и updated_at
    public $timestamps = false;
    
    protected $primaryKey = 'user_id';
    
    protected static function newFactory(): Factory
    {
        return UserFilmFactory::new();
    }
}

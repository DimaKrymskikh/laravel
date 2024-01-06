<?php

namespace App\Models\Dvd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmActor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films_actors';
    
    // При сохранении данных не передаются поля created_at и updated_at
    public $timestamps = false;
    
    protected $primaryKey = 'actor_id';
}

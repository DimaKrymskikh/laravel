<?php

namespace App\Models\Dvd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmActor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films_actors';
}

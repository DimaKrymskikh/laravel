<?php

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dvd\Film;

class User extends Model
{
    use HasFactory;
    
    protected $table = 'person.users';
    
    public function film(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'person.users_films', 'user_id', 'film_id');
    }
}

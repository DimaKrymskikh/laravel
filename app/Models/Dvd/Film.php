<?php

namespace App\Models\Dvd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Language;
use App\Models\Dvd\Actor;
use App\Models\Person\User;

class Film extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films';
    
    public function language(): HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
    
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'dvd.films_actors', 'film_id', 'actor_id');
    }
}

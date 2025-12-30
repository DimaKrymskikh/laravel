<?php

namespace App\Models\Dvd;

use App\Support\Collections\Dvd\FilmActorCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Модель таблицы 'dvd.films_actors', связующей таблицы 'dvd.actors' и 'dvd.films'
 * 
 * @property int film_id Ссылка на dvd.films.id.
 * @property int actor_id Ссылка на dvd.actors.id.
 * @property string $created_at
 */
class FilmActor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films_actors';
    
    // При сохранении данных не передаются поля created_at и updated_at
    public $timestamps = false;
    
    protected $primaryKey = 'actor_id';
    
    public function newCollection(array $models = []): FilmActorCollection
    {
        return new FilmActorCollection($models);
    }
}

<?php

namespace App\Models\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Actor;
use App\Models\Thesaurus\Language;
use App\Support\Collections\Dvd\FilmCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Фильмы (таблица 'dvd.films')
 * 
 * @property int $id Первичный ключ таблицы 'dvd.films'.
 * @property string $title Название фильма.
 * @property string $description Краткое описание фильма.
 * @property int $release_year Год выхода фильма.
 * @property int $language_id id языка, на котором снят фильм (ссылка на thesaurus.languages.id).
 * @property string $created_at
 * @property string $updated_at
 */
class Film extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films';
    
    public $timestamps = false;
    
    public function newCollection(array $models = []): FilmCollection
    {
        return new FilmCollection($models);
    }
    
    public function language(): HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
    
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'dvd.films_actors', 'film_id', 'actor_id');
    }
    
    public function scopeFilter(Builder $query, FilmFilterDto $dto): Builder
    {
        return $query
                ->when($dto->title, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($dto->description, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->when((string) $dto->releaseYear, function (Builder $query, string $releaseYear) {
                    $query->where('release_year', 'ILIKE', "%$releaseYear%");
                });
    }
}

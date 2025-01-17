<?php

namespace App\Models\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\FilmFilterDto;
use App\Models\Dvd\Actor;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Film extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films';
    
    public $timestamps = false;
    
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

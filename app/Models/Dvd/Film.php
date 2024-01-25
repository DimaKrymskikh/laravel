<?php

namespace App\Models\Dvd;

use App\Models\Dvd\Actor;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

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
    
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query
                ->when($request->title_filter, function (Builder $query, string $title) {
                    $query->where('title', 'ILIKE', "%$title%");
                })
                ->when($request->description_filter, function (Builder $query, string $description) {
                    $query->where('description', 'ILIKE', "%$description%");
                })
                ->when($request->release_year_filter, function (Builder $query, string $release_year) {
                    $query->where('release_year', 'ILIKE', "%$release_year%");
                });
    }
}

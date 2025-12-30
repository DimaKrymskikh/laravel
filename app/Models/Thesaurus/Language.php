<?php

namespace App\Models\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Support\Collections\Thesaurus\LanguageCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Языки народов (таблица 'thesaurus.languages')
 * 
 * @property int $id Первичный ключ таблицы 'thesaurus.languages'.
 * @property string $name Название.
 * @property string $created_at
 * @property string $updated_at
 */
class Language extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $table = 'thesaurus.languages';
    
    public function newCollection(array $models = []): LanguageCollection
    {
        return new LanguageCollection($models);
    }
    
    public function scopeFilter(Builder $query, LanguageFilterDto $dto): Builder
    {
        return $query
                ->when($dto->name, function (Builder $query, string $name) {
                    $query->where('name', 'ILIKE', "%$name%");
                });
    }
}

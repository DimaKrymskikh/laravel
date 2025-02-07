<?php

namespace App\Models\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $table = 'thesaurus.languages';
    
    public function scopeFilter(Builder $query, LanguageFilterDto $dto): Builder
    {
        return $query
                ->when($dto->name, function (Builder $query, string $name) {
                    $query->where('name', 'ILIKE', "%$name%");
                });
    }
}

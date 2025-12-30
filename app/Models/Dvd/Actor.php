<?php

namespace App\Models\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Support\Collections\Dvd\ActorCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Актёры (таблица 'dvd.actors')
 * 
 * @property int $id Первичный ключ таблицы 'dvd.actors'.
 * @property string $first_name Имя актёра.
 * @property string $last_name Фамилия актёра.
 * @property string $created_at
 * @property string $updated_at
 */
class Actor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.actors';
    
    public $timestamps = false;
    
    public function newCollection(array $models = []): ActorCollection
    {
        return new ActorCollection($models);
    }
    
    public function scopeFilter(Builder $query, ActorFilterDto $dto): Builder
    {
        return $query
            ->when($dto->name, function (Builder $query, string $name) {
                $query->whereRaw("concat(first_name, ' ', last_name) ILIKE ?", ["%$name%"]);
            });
    }
}

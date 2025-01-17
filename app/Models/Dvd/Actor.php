<?php

namespace App\Models\Dvd;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Actor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.actors';
    
    public $timestamps = false;
    
    public function scopeFilter(Builder $query, ActorFilterDto $dto): Builder
    {
        return $query
            ->when($dto->name, function (Builder $query, string $name) {
                $query->whereRaw("concat(first_name, ' ', last_name) ILIKE ?", ["%$name%"]);
            });
    }
}

<?php

namespace App\Models\Dvd;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Actor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.actors';
    
    public $timestamps = false;
    
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->name, function (Builder $query, string $name) {
                $query->whereRaw("concat(first_name, ' ', last_name) ILIKE ?", ["%$name%"]);
            });
    }
}

<?php

namespace App\Models\Dvd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Language;

class Film extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.films';
    
    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}

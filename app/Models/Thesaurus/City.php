<?php

namespace App\Models\Thesaurus;

use Database\Factories\Thesaurus\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    
    protected $table = 'thesaurus.cities';
    
    public $timestamps = false;
    
    protected static function newFactory(): Factory
    {
        return CityFactory::new();
    }
}

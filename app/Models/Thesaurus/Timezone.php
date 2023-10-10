<?php

namespace App\Models\Thesaurus;

use Database\Factories\Thesaurus\TimezoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory;
    
    protected $table = 'thesaurus.timezones';
    
    public $timestamps = false;
    
    protected static function newFactory(): Factory
    {
        return TimezoneFactory::new();
    }
}

<?php

namespace App\Models\Thesaurus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $table = 'thesaurus.languages';
}

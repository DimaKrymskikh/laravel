<?php

namespace App\Models\Dvd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use HasFactory;
    
    protected $table = 'dvd.actors';
}

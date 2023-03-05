<?php

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dvd\Film;

class User extends Model
{
    use HasFactory;
    
    protected $table = 'person.users';
}

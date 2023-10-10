<?php

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCity extends Model
{
    use HasFactory;
    
    protected $table = 'person.users_cities';
    
    // При сохранении данных не передаются поля created_at и updated_at
    public $timestamps = false;
    
    protected $primaryKey = 'user_id';
}

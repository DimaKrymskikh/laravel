<?php

namespace App\Models;

use App\Models\Dvd\Film;
use App\Models\Thesaurus\City;
use App\Notifications\Auth\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'person.users';
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Задаёт email-свойство при уведомлениях через mail-канал.
     * 
     * @param Notification $notification
     * @return string
     */
    public function routeNotificationForMail(Notification $notification): string
    {
        return $this->email;
    }
    
    /**
     * Отправляет пользователю уведомление о сбросе пароля.
     * 
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token, $this->email));
    }
    
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'person.users_cities', 'user_id', 'city_id');
    }
    
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'person.users_films', 'user_id', 'film_id');
    }
}

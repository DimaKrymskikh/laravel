<?php

namespace App\Models;

use App\Models\Dvd\Film;
use App\Models\Person\UserFilm;

/**
 * Cодержит методы, возвращающие модель User
 */
trait UserTrait
{
    /**
     * Возвращает модель User с добавленным свойством title (название фильма)
     * 
     * @param UserFilm $userFilm
     * @return User
     */
    public function getUserWithFilm(UserFilm $userFilm): User
    {
        $user = User::find($userFilm->user_id);
        
        $user->title = Film::select('title')
                ->where('id', $userFilm->film_id)
                ->first()->title;
        
        return $user;
    }
}

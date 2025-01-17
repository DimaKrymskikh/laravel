<?php

namespace App\Services\Database\Person;

use App\Models\Person\UserFilm;
use App\Queries\Dvd\FilmQueries;
use App\Queries\Person\UserFilmQueries;
use Illuminate\Validation\ValidationException;

class UserFilmService
{
    public function __construct(
            private FilmQueries $filmQueries,
            private UserFilmQueries $userFilmQueries,
    ) {
    }
    
    public function create(int $userId, int $filmId): bool
    {
        // Проверка наличия в таблице 'person.users_films' пары первичных ключей (user_id, film_id)
        if ($this->userFilmQueries->checkUserFilm($userId, $filmId)) {
            // Если пара существует, выбрасываем исключение
            $film = $this->filmQueries->getFilmById($filmId);
            throw ValidationException::withMessages([
                'message' => trans("user.film.message", [
                    'film' => $film->title
                ]),
            ]);
        }
        
        // Новую пару записываем в таблицу 'person.users_films'
        $userFilm = new UserFilm;
        $userFilm->user_id = $userId;
        $userFilm->film_id = $filmId;
        
        return $userFilm->save();
    }
    
    public function delete(int $userId, int $filmId): bool
    {
        return $this->userFilmQueries->getUserFilm($userId, $filmId)->delete();
    }
}

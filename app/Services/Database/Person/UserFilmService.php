<?php

namespace App\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserFilm;
use App\Modifiers\Person\UsersFilms\UserFilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;

final class UserFilmService
{
    public function __construct(
            private UserFilmModifiersInterface $userFilmModifiers,
            private FilmQueriesInterface $filmQueries,
            private UserFilmQueriesInterface $userFilmQueries,
    ) {
    }
    
    public function create(int $userId, int $filmId): void
    {
        // Проверка наличия в таблице 'person.users_films' пары первичных ключей (user_id, film_id)
        if ($this->userFilmQueries->exists($userId, $filmId)) {
            // Если пара существует, выбрасываем исключение
            $filmTitle = $this->filmQueries->getById($filmId)->title;
            throw new DatabaseException("Фильм '$filmTitle' уже находится в вашей коллекции.");
        }
        
        // Новую пару записываем в таблицу 'person.users_films'
        $this->userFilmModifiers->save(new UserFilm(), $userId, $filmId);
    }
    
    public function delete(int $userId, int $filmId): void
    {
        // Если пара не существует, выбрасываем исключение
        if (!$this->userFilmQueries->exists($userId, $filmId)) {
            $filmTitle = $this->filmQueries->getById($filmId)->title;
            throw new DatabaseException("Фильма '$filmTitle' нет в вашей коллекции. Удаление невозможно.");
        }
        
        $this->userFilmModifiers->delete($userId, $filmId);
    }
}

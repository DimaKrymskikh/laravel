<?php

namespace App\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UsersFilms\UserFilmModifiersInterface;
use App\Queries\Dvd\Films\FilmQueriesInterface;
use App\Queries\Person\UsersFilms\UserFilmQueriesInterface;
use App\Services\Database\Person\Dto\UserFilmDto;

final class UserFilmService
{
    public function __construct(
            private UserFilmModifiersInterface $userFilmModifiers,
            private FilmQueriesInterface $filmQueries,
            private UserFilmQueriesInterface $userFilmQueries,
    ) {
    }
    
    public function create(UserFilmDto $dto): void
    {
        // Проверка наличия в таблице 'person.users_films' пары первичных ключей (user_id, film_id)
        if ($this->userFilmQueries->exists($dto)) {
            // Если пара существует, выбрасываем исключение
            $filmTitle = $this->filmQueries->getById($dto->filmId)->title;
            throw new DatabaseException("Фильм '$filmTitle' уже находится в вашей коллекции.");
        }
        
        // Новую пару записываем в таблицу 'person.users_films'
        $this->userFilmModifiers->save($dto);
    }
    
    public function delete(UserFilmDto $dto): void
    {
        // Если пара не существует, выбрасываем исключение
        if (!$this->userFilmQueries->exists($dto)) {
            $filmTitle = $this->filmQueries->getById($dto->filmId)->title;
            throw new DatabaseException("Фильма '$filmTitle' нет в вашей коллекции. Удаление невозможно.");
        }
        
        $this->userFilmModifiers->remove($dto);
    }
}

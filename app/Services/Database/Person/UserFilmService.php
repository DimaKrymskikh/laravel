<?php

namespace App\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserFilm;
use App\Repositories\Dvd\FilmRepositoryInterface;
use App\Repositories\Person\UserFilmRepositoryInterface;

final class UserFilmService
{
    public function __construct(
            private FilmRepositoryInterface $filmRepository,
            private UserFilmRepositoryInterface $userFilmRepository,
    ) {
    }
    
    public function create(int $userId, int $filmId): void
    {
        // Проверка наличия в таблице 'person.users_films' пары первичных ключей (user_id, film_id)
        if ($this->userFilmRepository->exists($userId, $filmId)) {
            // Если пара существует, выбрасываем исключение
            $filmTitle = $this->filmRepository->getById($filmId)->title;
            throw new DatabaseException("Фильм '$filmTitle' уже находится в вашей коллекции.");
        }
        
        // Новую пару записываем в таблицу 'person.users_films'
        $this->userFilmRepository->save(new UserFilm(), $userId, $filmId);
    }
    
    public function delete(int $userId, int $filmId): void
    {
        // Если пара не существует, выбрасываем исключение
        if (!$this->userFilmRepository->exists($userId, $filmId)) {
            $filmTitle = $this->filmRepository->getById($filmId)->title;
            throw new DatabaseException("Фильма '$filmTitle' нет в вашей коллекции. Удаление невозможно.");
        }
        
        $this->userFilmRepository->delete($userId, $filmId);
    }
}

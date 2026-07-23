<?php

namespace App\Services\Database\Person;

use App\Events\AddFilmInUserList;
use App\Events\RemoveFilmFromUserList;
use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UserFilmModifiers;
use App\Queries\Dvd\FilmQueries;
use App\Queries\Person\UserFilmQueries;
use App\Services\Database\Person\Dto\UserFilmDto;
use App\Services\ServiceManagerInterface;
use Illuminate\Contracts\Events\Dispatcher;

final class UserFilmService
{
    private UserFilmModifiers $userFilmModifiers;
    private FilmQueries $filmQueries;
    private UserFilmQueries $userFilmQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
            private Dispatcher $dispatcher,
    ) {
        $this->userFilmModifiers = $this->serviceManager->getQueriesOrModifiers(UserFilmModifiers::class);
        $this->filmQueries = $this->serviceManager->getQueriesOrModifiers(FilmQueries::class);
        $this->userFilmQueries = $this->serviceManager->getQueriesOrModifiers(UserFilmQueries::class);
    }
    
    /**
     * Создаёт новую запись в таблице 'person.users_films'.
     * Пользователю отправляется уведомление о добавлении в его коллекцию нового фильма.
     * 
     * @param UserFilmDto $dto
     * @return void
     * @throws DatabaseException
     */
    public function create(UserFilmDto $dto): void
    {
        $filmTitle = $this->filmQueries->getById($dto->filmId)->title;
        
        // Проверка наличия в таблице 'person.users_films' пары первичных ключей (user_id, film_id)
        if ($this->userFilmQueries->exists($dto)) {
            throw new DatabaseException("Фильм '$filmTitle' уже находится в вашей коллекции.");
        }
        
        // Новую пару записываем в таблицу 'person.users_films'
        $this->userFilmModifiers->save($dto);
        
        // Если запись была успешной, пользователь получает оповещение
        $this->dispatcher->dispatch(new AddFilmInUserList($dto->userId, $filmTitle));
    }
    
    /**
     * Удаляет запись из таблицы 'person.users_films'.
     * Пользователю отправляется уведомление об удалении фильма из его коллекции.
     * 
     * @param UserFilmDto $dto
     * @return void
     * @throws DatabaseException
     */
    public function delete(UserFilmDto $dto): void
    {
        $filmTitle = $this->filmQueries->getById($dto->filmId)->title;
        
        // Если пара не существует, выбрасываем исключение
        if (!$this->userFilmQueries->exists($dto)) {
            throw new DatabaseException("Фильма '$filmTitle' нет в вашей коллекции. Удаление невозможно.");
        }
        
        $this->userFilmModifiers->remove($dto);
        
        // При успешном удалении фильма пользователь получает оповещение
        $this->dispatcher->dispatch(new RemoveFilmFromUserList($dto->userId, $filmTitle));
    }
}

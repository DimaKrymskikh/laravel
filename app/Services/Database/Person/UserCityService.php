<?php

namespace App\Services\Database\Person;

use App\Events\AddCityInWeatherList;
use App\Events\RemoveCityFromWeatherList;
use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Person\Dto\UserCityDto;
use Illuminate\Contracts\Events\Dispatcher;

final class UserCityService
{
    public function __construct(
            private UserCityModifiersInterface $userCityModifiers,
            private UserCityQueriesInterface $userCityQueries,
            private CityQueriesInterface $cityQueries,
            private Dispatcher $dispatcher,
    ) {
    }
    
    /**
     * Добавляет город в список пользователя.
     * 
     * @param UserCityDto $dto
     * @return void
     * @throws DatabaseException
     */
    public function create(UserCityDto $dto): void
    {
        $cityName = $this->cityQueries->getById($dto->cityId)->name;
        
        if ($this->userCityQueries->exists($dto)) {
            throw new DatabaseException("Город '$cityName' уже выбран для просмотра погоды.");
        }
        
        $this->userCityModifiers->save($dto);
        
        $this->dispatcher->dispatch(new AddCityInWeatherList($dto->userId, $cityName));
    }
    
    /**
     * Удаляет город из списка пользователя.
     * 
     * @param UserCityDto $dto
     * @return void
     * @throws DatabaseException
     */
    public function delete(UserCityDto $dto): void
    {
        $cityName = $this->cityQueries->getById($dto->cityId)->name;
        
        if (!$this->userCityQueries->exists($dto)) {
            throw new DatabaseException("Города '$cityName' уже нет в списке просмотра погоды.");
        }
        
        $this->userCityModifiers->remove($dto);
        
        $this->dispatcher->dispatch(new RemoveCityFromWeatherList($dto->userId, $cityName));
    }
}

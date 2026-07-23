<?php

namespace App\Services\Database\Person;

use App\Events\AddCityInWeatherList;
use App\Events\RemoveCityFromWeatherList;
use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UserCityModifiers;
use App\Queries\Person\UserCityQueries;
use App\Queries\Thesaurus\CityQueries;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\ServiceManagerInterface;
use Illuminate\Contracts\Events\Dispatcher;

final class UserCityService
{
    private UserCityModifiers $userCityModifiers;
    private UserCityQueries $userCityQueries;
    private CityQueries $cityQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
            private Dispatcher $dispatcher,
    ) {
        $this->userCityModifiers = $this->serviceManager->getQueriesOrModifiers(UserCityModifiers::class);
        $this->userCityQueries = $this->serviceManager->getQueriesOrModifiers(UserCityQueries::class);
        $this->cityQueries = $this->serviceManager->getQueriesOrModifiers(CityQueries::class);
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

<?php

namespace App\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Services\Database\Person\Dto\UserCityDto;

final class UserCityService
{
    public function __construct(
            private UserCityModifiersInterface $userCityModifiers,
            private UserCityQueriesInterface $userCityQueries,
            private CityQueriesInterface $cityQueries,
    ) {
    }
    
    public function create(UserCityDto $dto): void
    {
        if ($this->userCityQueries->exists($dto)) {
            $cityName = $this->cityQueries->getById($dto->cityId)->name;
            throw new DatabaseException("Город '$cityName' уже выбран для просмотра погоды.");
        }
        
        $this->userCityModifiers->save($dto);
    }
    
    public function delete(UserCityDto $dto): void
    {
        if (!$this->userCityQueries->exists($dto)) {
            $cityName = $this->cityQueries->getById($dto->cityId)->name;
            throw new DatabaseException("Города '$cityName' уже нет в списке просмотра погоды.");
        }
        
        $this->userCityModifiers->remove($dto);
    }
}

<?php

namespace App\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserCity;
use App\Modifiers\Person\UsersCities\UserCityModifiersInterface;
use App\Queries\Person\UsersCities\UserCityQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;

final class UserCityService
{
    public function __construct(
            private UserCityModifiersInterface $userCityModifiers,
            private UserCityQueriesInterface $userCityQueries,
            private CityQueriesInterface $cityQueries,
    ) {
    }
    
    public function create(int $userId, int $cityId): void
    {
        if ($this->userCityQueries->exists($userId, $cityId)) {
            $cityName = $this->cityQueries->getById($cityId)->name;
            throw new DatabaseException("Город '$cityName' уже выбран для просмотра погоды.");
        }
        
        $this->userCityModifiers->save(new UserCity(), $userId, $cityId);
    }
    
    public function delete(int $userId, int $cityId): void
    {
        if (!$this->userCityQueries->exists($userId, $cityId)) {
            $cityName = $this->cityQueries->getById($cityId)->name;
            throw new DatabaseException("Города '$cityName' уже нет в списке просмотра погоды.");
        }
        
        $this->userCityModifiers->delete($userId, $cityId);
    }
}

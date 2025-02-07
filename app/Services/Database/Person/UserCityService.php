<?php

namespace App\Services\Database\Person;

use App\Exceptions\DatabaseException;
use App\Models\Person\UserCity;
use App\Repositories\Person\UserCityRepositoryInterface;
use App\Repositories\Thesaurus\CityRepositoryInterface;

final class UserCityService
{
    public function __construct(
            private UserCityRepositoryInterface $userCityRepository,
            private CityRepositoryInterface $cityRepository,
    ) {
    }
    
    public function create(int $userId, int $cityId): void
    {
        if ($this->userCityRepository->exists($userId, $cityId)) {
            $cityName = $this->cityRepository->getById($cityId)->name;
            throw new DatabaseException("Город '$cityName' уже выбран для просмотра погоды.");
        }
        
        $this->userCityRepository->save(new UserCity(), $userId, $cityId);
    }
    
    public function delete(int $userId, int $cityId): void
    {
        if (!$this->userCityRepository->exists($userId, $cityId)) {
            $cityName = $this->cityRepository->getById($cityId)->name;
            throw new DatabaseException("Города '$cityName' уже нет в списке просмотра погоды.");
        }
        
        $this->userCityRepository->delete($userId, $cityId);
    }
}

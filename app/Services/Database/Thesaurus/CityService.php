<?php

namespace App\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class CityService
{
    const VARIABLE_TABLE_FIELDS = ['name', 'timezone_id'];
    
    public function __construct(
        private CityRepositoryInterface $cityRepository,
    ) {
    }
    
    public function create(string $name, int $openWeatherId): void
    {
        $this->cityRepository->save(new City(), $name, $openWeatherId);
    }
    
    public function update(int $cityId, string $field, mixed $value): void
    {
        if (!in_array($field, self::VARIABLE_TABLE_FIELDS)) {
            throw new DatabaseException("Поле '$field' таблицы 'thesaurus.cities' нельзя изменять.");
        }
        
        $fieldValue = match ($field) {
            'timezone_id' => $value ?: null,
            default => $value
        };
        
        $city = $this->cityRepository->getById($cityId);
        $this->cityRepository->saveField($city, $field, $fieldValue);
    }
    
    public function delete(int $cityId): void
    {
        if (!$this->cityRepository->exists($cityId)) {
            throw new DatabaseException("В таблице 'thesaurus.cities' нет записи с id=$cityId");
        }
        
        $this->cityRepository->delete($cityId);
    }
    
    public function getCityById(int $cityId): City
    {
        return $this->cityRepository->getById($cityId);
    }
    
    public function getAllCitiesList(): Collection
    {
        return $this->cityRepository->getList();
    }
    
    public function getListWithAvailableByUserId(int $userId): Collection
    {
        return $this->cityRepository->getListWithAvailableByUserId($userId);
    }
    
    /**
     * Находит и возвращает город по полю thesaurus.cities.open_weather_id
     * 
     * @param type $openWeatherId
     * @return City
     */
    public function findCityByOpenWeatherId($openWeatherId): City
    {
        $city = $this->cityRepository->getByOpenWeatherId($openWeatherId);
        
        return $city ?? throw new DatabaseException("В таблице 'thesaurus.cities' нет городов с полем open_weather_id = $openWeatherId");
    }
}

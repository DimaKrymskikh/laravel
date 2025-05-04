<?php

namespace App\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Modifiers\Thesaurus\Cities\CityModifiersInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

final class CityService
{
    const VARIABLE_TABLE_FIELDS = ['name', 'timezone_id'];
    
    public function __construct(
        private CityModifiersInterface $cityModifiers,
        private CityQueriesInterface $cityQueries,
    ) {
    }
    
    public function create(string $name, int $openWeatherId): void
    {
        $this->cityModifiers->save(new City(), $name, $openWeatherId);
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
        
        $city = $this->cityQueries->getById($cityId);
        $this->cityModifiers->saveField($city, $field, $fieldValue);
    }
    
    public function delete(int $cityId): void
    {
        if (!$this->cityQueries->exists($cityId)) {
            throw new DatabaseException("В таблице 'thesaurus.cities' нет записи с id=$cityId");
        }
        
        $this->cityModifiers->delete($cityId);
    }
    
    public function getCityById(int $cityId): City
    {
        return $this->cityQueries->getById($cityId);
    }
    
    public function getAllCitiesList(): Collection
    {
        return $this->cityQueries->getList();
    }
    
    public function getListWithAvailableByUserId(int $userId): Collection
    {
        return $this->cityQueries->getListWithAvailableByUserId($userId);
    }
    
    /**
     * Находит и возвращает город по полю thesaurus.cities.open_weather_id
     * 
     * @param type $openWeatherId
     * @return City
     */
    public function findCityByOpenWeatherId($openWeatherId): City
    {
        $city = $this->cityQueries->getByOpenWeatherId($openWeatherId);
        
        return $city ?? throw new DatabaseException("В таблице 'thesaurus.cities' нет городов с полем open_weather_id = $openWeatherId");
    }
}

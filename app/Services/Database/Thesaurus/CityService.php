<?php

namespace App\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Modifiers\Thesaurus\Cities\CityModifiersInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Support\Collections\Thesaurus\CityCollection;

final class CityService
{
    const VARIABLE_TABLE_FIELDS = ['name', 'timezone_id'];
    
    public function __construct(
        private CityModifiersInterface $cityModifiers,
        private CityQueriesInterface $cityQueries,
    ) {
    }
    
    public function create(string $name, int $openWeatherId): City
    {
        $city = new City();
        $city->name = $name;
        $city->open_weather_id = $openWeatherId;
        
        $this->cityModifiers->save($city);
        
        return $city;
    }
    
    public function update(int $cityId, string $field, mixed $value): City
    {
        if (!in_array($field, self::VARIABLE_TABLE_FIELDS)) {
            throw new DatabaseException("Поле '$field' таблицы 'thesaurus.cities' нельзя изменять.");
        }
        
        $city = $this->cityQueries->getById($cityId);

        $fieldValue = match ($field) {
            'timezone_id' => $value ?: null,
            default => $value
        };
        $city->$field = $fieldValue;
        
        $this->cityModifiers->save($city);
        
        return $city;
    }
    
    public function delete(int $cityId): void
    {
        $city = $this->cityQueries->getById($cityId);
        
        $this->cityModifiers->remove($city);
    }
    
    public function getCityById(int $cityId): City
    {
        return $this->cityQueries->getById($cityId);
    }
    
    /**
     * Получить все ряды таблицы 'thesaurus.cities'
     * 
     * @return CityCollection
     */
    public function getAllCitiesList(): CityCollection
    {
        return $this->cityQueries->getList();
    }
    
    public function getListWithAvailableByUserId(int $userId): CityCollection
    {
        return $this->cityQueries->getListWithAvailableByUserId($userId);
    }
    
    /**
     * Находит и возвращает город по полю thesaurus.cities.open_weather_id
     * 
     * @param int $openWeatherId id-города в сервисе OpenWeather
     * @return City
     */
    public function findCityByOpenWeatherId(int $openWeatherId): City
    {
        return $this->cityQueries->getByOpenWeatherId($openWeatherId);
    }
}

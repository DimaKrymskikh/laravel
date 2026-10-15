<?php

namespace App\Services\Database\Thesaurus;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Modifiers\Thesaurus\CityModifiers;
use App\Queries\Thesaurus\CityQueries;
use App\Support\Collections\Thesaurus\CityCollection;

final class CityService implements InfoAboutCitiesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.cities' нет записи с id=%d";
    public const NOT_RECORD_WITH_OPEN_WEATHER_ID = "В таблице 'thesaurus.cities' нет записи с open_weather_id=%d";
    public const VARIABLE_TABLE_FIELDS = ['name', 'timezone_id'];
    
    private CityModifiers $cityModifiers;
    public readonly CityQueries $cityQueries;

    public function __construct(
            private readonly ServiceManagerInterface $serviceManager,
    ) {
        $this->cityModifiers = $this->serviceManager->getQueriesOrModifiers(CityModifiers::class);
        $this->cityQueries = $this->serviceManager->getQueriesOrModifiers(CityQueries::class);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCityById(int $id): City
    {
        return $this->cityQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCityByOpenWeatherId(int $openWeatherId): City
    {
        return $this->cityQueries->findByOpenWeatherId($openWeatherId)
                ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_OPEN_WEATHER_ID, $openWeatherId));
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
        
        $city = $this->getCityById($cityId);

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
        $city = $this->getCityById($cityId);
        
        $this->cityModifiers->remove($city);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAllCitiesList(): CityCollection
    {
        return $this->cityQueries->getList();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getListWithAvailableByUserId(int $userId): CityCollection
    {
        return $this->cityQueries->getListWithAvailableByUserId($userId);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCitiesWithWeatherByUser(User $user): CityCollection
    {
        return $this->cityQueries->getByUserWithWeather($user);
    }
}

<?php

namespace App\Contracts\Services\Database\Thesaurus;

use App\Models\Thesaurus\City;
use App\Models\User;
use App\Support\Collections\Thesaurus\CityCollection;

interface InfoAboutCitiesInterface 
{
    /**
     * Возвращает ORM-модель города по id города
     * 
     * @param int $id
     * @return City
     */
    public function getCityById(int $id): City;
    
    /**
     * Возвращает ORM-модель города по id-города в сервисе OpenWeather
     * 
     * @param int $openWeatherId id города в сервисе OpenWeather
     * @return City
     */
    public function getCityByOpenWeatherId(int $openWeatherId): City;
    
    /**
     * Получить все ряды таблицы 'thesaurus.cities'
     * 
     * @return CityCollection
     */
    public function getAllCitiesList(): CityCollection;
    
    /**
     * Получает коллекцию городов, доступную пользователю.
     * 
     * @param int $userId id пользователя.
     * @return CityCollection
     */
    public function getListWithAvailableByUserId(int $userId): CityCollection;
    
    /**
     * Возвращает города пользователя с текущей погодой. 
     * 
     * @param User $user
     * @return CityCollection
     */
    public function getCitiesWithWeatherByUser(User $user): CityCollection;
}

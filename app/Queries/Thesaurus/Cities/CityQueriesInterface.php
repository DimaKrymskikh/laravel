<?php

namespace App\Queries\Thesaurus\Cities;

use App\Models\Thesaurus\City;
use App\Models\User;
use App\Queries\QueriesInterface;
use App\Support\Collections\Thesaurus\CityCollection;
use Illuminate\Database\Eloquent\Collection;

interface CityQueriesInterface extends QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.cities' нет записи с id=%d";
    public const NOT_RECORD_WITH_OPEN_WEATHER_ID = "В таблице 'thesaurus.cities' нет записи с open_weather_id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    /**
     * Получить все ряды таблицы 'thesaurus.cities'
     * 
     * @return CityCollection
     */
    public function getList(): CityCollection;
    
    public function getByOpenWeatherId($openWeatherId): City;
    
    public function getByUserWithWeather(User $user): Collection;
    
    public function getListWithAvailableByUserId(int $userId): CityCollection;
    
    /**
     * Извлекает по частям все данные таблицы 'thesaurus.cities'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void;
}

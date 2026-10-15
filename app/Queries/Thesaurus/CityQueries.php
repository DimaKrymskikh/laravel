<?php

namespace App\Queries\Thesaurus;

use App\Models\Thesaurus\City;
use App\Models\User;
use App\Queries\ModelQueries;
use App\Support\Collections\Thesaurus\CityCollection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class CityQueries extends ModelQueries
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    protected function setClassName(): void
    {
        $this->className = City::class;
    }
    
    /**
     * Из таблицы 'thesaurus.cities' извлекает город по id-города в сервисе OpenWeather.
     * 
     * @param int $openWeatherId
     * @return City|null
     */
    public function findByOpenWeatherId(int $openWeatherId): City|null
    {
        return City::where('open_weather_id', $openWeatherId)->first();
    }
    
    /**
     * Получить все ряды таблицы 'thesaurus.cities'
     * 
     * @return CityCollection
     */
    public function getList(): CityCollection
    {
        return City::select('id', 'name', 'open_weather_id', 'timezone_id')
                    ->orderBy('name')
                    ->get();
    }
    
    /**
     * Возвращает города пользователя с текущей погодой.
     * 
     * @param User $user
     * @return CityCollection
     */
    public function getByUserWithWeather(User $user): CityCollection
    {
        $user->load([
                'cities' => function (Builder $query) {
                    $query
                        ->select('id', 'name', 'open_weather_id', 'timezone_id')
                        ->orderBy('name');
                },
                'cities.weather:city_id,weather_description,main_temp,main_feels_like,main_pressure,main_humidity,visibility,wind_speed,wind_deg,clouds_all,created_at',
                'cities.timezone:id,name'
            ]);
                
        return $user->cities;
    }
    
    /**
     * Получает коллекцию городов, доступную пользователю.
     * 
     * @param int $userId id пользователя.
     * @return CityCollection
     */
    public function getListWithAvailableByUserId(int $userId): CityCollection
    {
        return City::select('id', 'name', 'timezone_id')
                    ->with('timezone:id,name')
                    ->leftJoin('person.users_cities', function(JoinClause $join) use ($userId) {
                        $join->on('person.users_cities.city_id', 'thesaurus.cities.id')
                            ->where('person.users_cities.user_id', $userId);
                    })
                    ->selectRaw('coalesce (person.users_cities.user_id::bool, false) AS "isAvailable"')
                    ->orderBy('name')
                    ->get();
    }
    
    /**
     * Извлекает по частям все данные таблицы 'thesaurus.cities'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void
    {
        City::select('id', 'name', 'open_weather_id', 'timezone_id')->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
}

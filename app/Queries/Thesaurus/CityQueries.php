<?php

namespace App\Queries\Thesaurus;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Queries\DBqueries;
use App\Queries\QueriesInterface;
use App\Support\Collections\Thesaurus\CityCollection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

class CityQueries extends DBqueries implements QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.cities' нет записи с id=%d";
    public const NOT_RECORD_WITH_OPEN_WEATHER_ID = "В таблице 'thesaurus.cities' нет записи с open_weather_id=%d";
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    public function exists(int $id): bool
    {
        return City::where('id', $id)->exists();
    }
    
    /**
     * Получить из таблицы 'thesaurus.cities' запись с первичным ключом id
     * 
     * @param int $id
     * @return City
     */
    public function getById(int $id): City
    {
        return City::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * Возвращает город по id-города в сервисе OpenWeather
     * 
     * @param int $openWeatherId id-города в сервисе OpenWeather
     * @return City
     */
    public function getByOpenWeatherId(int $openWeatherId): City
    {
        return City::where('open_weather_id', $openWeatherId)->first()
                ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_OPEN_WEATHER_ID, $openWeatherId));
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
     * @return Collection
     */
    public function getByUserWithWeather(User $user): Collection
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

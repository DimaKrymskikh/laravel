<?php

namespace App\Queries\Thesaurus\Cities;

use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\City;
use App\Models\User;
use App\Queries\DBqueries;
use App\Support\Collections\Thesaurus\CityCollection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

final class CityQueries extends DBqueries implements CityQueriesInterface
{
    public function exists(int $id): bool
    {
        return City::where('id', $id)->exists();
    }
    
    public function getById(int $id): City
    {
        return City::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getByOpenWeatherId(int $openWeatherId): City
    {
        return City::where('open_weather_id', $openWeatherId)->first()
                ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_OPEN_WEATHER_ID, $openWeatherId));
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getList(): CityCollection
    {
        return City::select('id', 'name', 'open_weather_id', 'timezone_id')
                    ->with('timezone:id,name')
                    ->orderBy('name')
                    ->get();
    }
    
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
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function getListInLazyById(\Closure $callback): void
    {
        City::select('id', 'name', 'open_weather_id', 'timezone_id')->orderBy('id')
            ->lazyById(self::NUMBER_OF_ITEMS_IN_CHUNCK, column: 'id')
            ->each($callback);
    }
}

<?php

namespace App\Queries\Thesaurus\Cities;

use App\Models\Thesaurus\City;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;

final class CityQueries implements CityQueriesInterface
{
    public function exists(int $id): bool
    {
        return City::where('id', $id)->exists();
    }
    
    public function getById(int $id): City
    {
        return City::find($id);
    }
    
    public function getByOpenWeatherId($openWeatherId): City|null
    {
        return City::where('open_weather_id', $openWeatherId)->first();
    }
    
    public function getList(): Collection
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
    
    public function getListWithAvailableByUserId(int $userId): Collection
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
}

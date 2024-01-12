<?php

namespace App\Repositories\Thesaurus;

use App\Models\Thesaurus\City;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class CityRepository
{
    public function getWeatherForCitiesOfAuth(Request $request): Collection
    {
        return City::with([
            'weatherFirst' => function (Builder $query) {
                $query->select(
                        'city_id',
                        'weather_description',
                        'main_temp',
                        'main_feels_like',
                        'main_pressure',
                        'main_humidity',
                        'visibility',
                        'wind_speed',
                        'wind_deg',
                        'clouds_all',
                        'created_at'
                    )->distinct('city_id')
                        ->orderBy('city_id')
                        ->orderBy('created_at', 'desc');
            },
            'timezone:id,name'
        ])->select('id', 'name', 'open_weather_id', 'timezone_id')
            ->join('person.users_cities', function(JoinClause $join) use ($request) {
                $join->on('person.users_cities.city_id', 'thesaurus.cities.id')
                    ->where('person.users_cities.user_id', $request->user()->id);
            })
            ->orderBy('name')
            ->get();
    }
}

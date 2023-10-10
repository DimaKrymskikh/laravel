<?php

namespace App\Http\Controllers\OpenWeather;

use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Http\Controllers\Controller;
use App\Models\Thesaurus\City;
use App\Support\Support\Timezone;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeatherController extends Controller implements TimezoneInterface
{
    use Timezone;
    
    public function index(Request $request): Response
    {
        $cities = City::with([
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
            ->orderBy('name')
            ->get();

        $this->setTimezone($cities);

        return Inertia::render('Auth/Weather', [
            'cities' => $cities,
            'user' => $request->user()
        ]);
    }
}

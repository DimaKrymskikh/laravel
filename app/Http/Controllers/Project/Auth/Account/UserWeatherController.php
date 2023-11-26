<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Http\Controllers\Controller;
use App\Models\ModelsFields;
use App\Models\Thesaurus\City;
use App\Support\Support\Timezone;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserWeatherController extends Controller implements TimezoneInterface
{
    use Timezone, ModelsFields;
    
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
            ->join('person.users_cities', function(JoinClause $join) use ($request) {
                $join->on('person.users_cities.city_id', 'thesaurus.cities.id')
                    ->where('person.users_cities.user_id', $request->user()->id);
            })
            ->orderBy('name')
            ->get();

        $this->setTimezone($cities);

        return Inertia::render('Auth/Account/UserWeather', [
            'cities' => $cities,
            'user' => $request->user()
        ]);
    }
    
    /**
     * Обновляет данные о погоде в городе с $city_id
     * 
     * @param Request $request
     * @param string $city_id
     * @return void
     */
    public function refresh(Request $request, string $city_id): void
    {
        $openWeatherId = $this->getModelField(City::class, 'open_weather_id', $city_id);
        
        Artisan::queue('get:weather', [
            'open_weather_id' => $openWeatherId,
            '--http' => true,
            '--user_id' => $request->user()->id,
        ]);
    }
}

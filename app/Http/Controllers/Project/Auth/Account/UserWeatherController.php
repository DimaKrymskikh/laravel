<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Http\Controllers\Controller;
use App\Models\Thesaurus\City;
use App\Repositories\Thesaurus\CityRepository;
use App\Support\Support\Timezone;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserWeatherController extends Controller implements TimezoneInterface
{
    use Timezone;
    
    public function __construct(
        private CityRepository $cities,
    )
    {}
    
    public function index(Request $request): Response
    {
        $cities = $this->cities->getWeatherForCitiesOfAuth($request);
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
     * @param int $city_id
     * @return void
     */
    public function refresh(Request $request, int $city_id): void
    {
        $openWeatherId = City::find($city_id)->open_weather_id;
        
        Artisan::queue('get:weather', [
            'open_weather_id' => $openWeatherId,
            '--http' => true,
            '--user_id' => $request->user()->id,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Http\Controllers\Controller;
use App\Http\Extraction\Thesaurus\Cities;
use App\Models\ModelsFields;
use App\Models\Thesaurus\City;
use App\Support\Support\Timezone;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserWeatherController extends Controller implements TimezoneInterface
{
    use Timezone, ModelsFields, Cities;
    
    public function index(Request $request): Response
    {
        $cities = $this->getWeatherForCitiesOfAuth($request);
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

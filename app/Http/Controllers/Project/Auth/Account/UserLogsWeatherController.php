<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logs\WeatherFilterRequest;
use App\Models\Thesaurus\City;
use App\Repositories\Logs\OpenWeatherWeatherRepository;
use Inertia\Inertia;
use Inertia\Response;

class UserLogsWeatherController extends Controller
{
    public function __construct(
        private OpenWeatherWeatherRepository $weather,
    )
    {}
    
    public function index(WeatherFilterRequest $request, int $city_id): Response
    {
        $city = City::select('id', 'name', 'timezone_id')->firstWhere('id', $city_id);

        return Inertia::render('Auth/Account/UserLogsWeather', [
            'weatherPage' => $this->weather->getWeatherPageByCity($request, $city),
            'city' => $city,
            'user' => $request->user()
        ]);
    }
}

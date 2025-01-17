<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logs\Filters\WeatherFilterRequest;
use App\Models\Thesaurus\City;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use Inertia\Inertia;
use Inertia\Response;

class UserLogsWeatherController extends Controller
{
    public function __construct(
        private OpenWeatherWeatherService $openWeatherWeatherService,
    ) {
    }
    
    /**
     * В аккаунте пользователя отрисовывает историю погоды в городе $cityId
     * 
     * @param WeatherFilterRequest $request
     * @param int $cityId
     * @return Response
     */
    public function index(WeatherFilterRequest $request, int $cityId): Response
    {
        $city = City::select('id', 'name', 'timezone_id')->firstWhere('id', $cityId);

        return Inertia::render('Auth/Account/UserLogsWeather', [
            'weatherPage' => $this->openWeatherWeatherService->getWeatherListForPageByCity($request->getPaginatorDto(), $request->getWeatherFilterDto(), $city),
            'city' => $city,
            'user' => $request->user()
        ]);
    }
}

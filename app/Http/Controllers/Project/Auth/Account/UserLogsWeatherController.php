<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\CommandHandlers\Database\Logs\Weather\WeatherListForPageCommandHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Logs\Filters\WeatherFilterRequest;
use App\Services\Database\Thesaurus\CityService;
use Inertia\Inertia;
use Inertia\Response;

class UserLogsWeatherController extends Controller
{
    public function __construct(
        private WeatherListForPageCommandHandler $weatherHandler,
        private CityService $cityService,
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
        $city = $this->cityService->getCityById($cityId);

        return Inertia::render('Auth/Account/UserLogsWeather', [
            'weatherPage' => $this->weatherHandler->handle($request->getPaginatorDto(), $request->getWeatherFilterDto(), $city),
            'city' => $city,
            'user' => $request->user()
        ]);
    }
}

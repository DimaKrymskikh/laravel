<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\Http\Controllers\Controller;
use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\OpenWeather\WeatherService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserWeatherController extends Controller
{
    public function __construct(
        private WeatherService $weatherService,
    )
    {}
    
    /**
     * В аккаунте пользователя отрисовывает список городов с погодой.
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Auth/Account/UserWeather', [
            'cities' => $this->weatherService->getWeatherInCitiesForAuthUserByUserId($request->user()->id),
            'user' => $request->user()
        ]);
    }
    
    /**
     * Обновляет данные о погоде в городе с $cityId.
     * Данные на фронтенде обновляет pusher.
     * 
     * @param Request $request
     * @param int $cityId
     * @return void
     */
    public function refresh(Request $request, int $cityId): void
    {
        $dto = new UserCityDto($request->user()->id, $cityId);
        $this->weatherService->refreshWeatherInCity($dto);
    }
}

<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Events\RefreshCityWeather;
use App\Http\Controllers\Controller;
use App\Models\Thesaurus\City;
use App\Repositories\OpenWeather\WeatherRepository;
use App\Repositories\Thesaurus\CityRepository;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\TimezoneService;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserWeatherController extends Controller
{
    public function __construct(
        private CityRepository $cities,
        private WeatherRepository $weatherRepository,
        private WeatherService $weatherService,
        private TimezoneService $timezoneService,
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
            'cities' => $this->cities->getWeatherForCitiesOfAuth($request),
            'user' => $request->user()
        ]);
    }
    
    /**
     * Обновляет данные о погоде в городе с $city_id
     * 
     * @param Request $request
     * @param int $city_id
     * @param WeatherService $weatherService
     * @param GetWeatherFromOpenWeatherCommandHandler $commandHandler
     * @return void
     */
    public function refresh(Request $request, int $city_id, GetWeatherFromOpenWeatherCommandHandler $commandHandler): void
    {
        $city = City::find($city_id);
        
        $response = $commandHandler->handle($city, $this->weatherRepository);
        
        if($response->status() !== 200) {
            return;
        }
        
        $dto = new WeatherDto($city_id, OpenWeatherObject::create($response->object()));

        $this->weatherService->updateOrCreate($dto);
        event(new RefreshCityWeather($city_id, $request->user()->id, $this->timezoneService));
    }
}

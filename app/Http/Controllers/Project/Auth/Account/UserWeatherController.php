<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\CommandHandlers\OpenWeather\GetWeatherFromOpenWeatherCommandHandler;
use App\Contracts\Support\Timezone as TimezoneInterface;
use App\DataTransferObjects\Database\OpenWeather\WeatherDto;
use App\Events\RefreshCityWeather;
use App\Http\Controllers\Controller;
use App\Models\Thesaurus\City;
use App\Repositories\OpenWeather\WeatherRepository;
use App\Repositories\Thesaurus\CityRepository;
use App\Services\Database\OpenWeather\WeatherService;
use App\Support\Support\Timezone;
use App\ValueObjects\ResponseObjects\OpenWeatherObject;
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
     * @param WeatherService $weatherService
     * @param GetWeatherFromOpenWeatherCommandHandler $commandHandler
     * @return void
     */
    public function refresh(Request $request, int $city_id, WeatherService $weatherService, GetWeatherFromOpenWeatherCommandHandler $commandHandler): void
    {
        $city = City::find($city_id);
        
        $response = $commandHandler->handle($city, new WeatherRepository());
        
        if($response->status() !== 200) {
            return;
        }
        
        $dto = new WeatherDto($city_id, OpenWeatherObject::create($response->object()));

        $weatherService->create($dto);
        event(new RefreshCityWeather($city_id, $request->user()->id));
    }
}

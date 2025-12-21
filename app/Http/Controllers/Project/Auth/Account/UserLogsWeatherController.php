<?php

namespace App\Http\Controllers\Project\Auth\Account;

use App\CommandHandlers\Database\Logs\Weather\WeatherListForPageCommandHandler;
use App\CommandHandlers\Database\Logs\Weather\WeatherStatisticsByPeriodicityIntervalCommandHandler;
use App\DataTransferObjects\Database\OpenWeather\WeatherStatisticsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Logs\Filters\WeatherFilterRequest;
use App\Services\Database\Thesaurus\CityService;
use App\ValueObjects\ArrayItems\TimeInterval;
use App\ValueObjects\DateString;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserLogsWeatherController extends Controller
{
    public function __construct(
        private WeatherListForPageCommandHandler $weatherHandler,
        private WeatherStatisticsByPeriodicityIntervalCommandHandler $weatherStatisticsHandler,
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

        return Inertia::render('Auth/Account/Weather/UserLogsWeather', [
            'weatherPage' => $this->weatherHandler->handle($request->getPaginatorDto(), $request->getWeatherFilterDto(), $city),
            'city' => $city,
            'user' => $request->user()
        ]);
    }
    
    /**
     * В аккаунте пользователя отрисовывает статистику погоды в городе $cityId
     * 
     * @param Request $request
     * @param int $cityId
     * @return Response
     */
    public function getStatistics(Request $request, int $cityId): Response
    {
        $city = $this->cityService->getCityById($cityId);

        $dto = new WeatherStatisticsDto(
                $city,
                DateString::create($request->input('datefrom'), false),
                DateString::create($request->input('dateto'), false),
                // Если параметр 'interval' отсутствует в запросе, будет использоваться интервал по умолчанию
                TimeInterval::create($request->input('interval')),
        );

        return Inertia::render('Auth/Account/Weather/WeatherStatistics', [
            // Если параметр 'interval' отсутствует в запросе отдаём пустой массив
            // Здесь нужно использовать $request->input('interval'), а не $dto->interval->value
            'weatherPage' => $request->input('interval') ? $this->weatherStatisticsHandler->handle($dto) : [],
            'city' => $city,
            'user' => $request->user()
        ]);
    }
}

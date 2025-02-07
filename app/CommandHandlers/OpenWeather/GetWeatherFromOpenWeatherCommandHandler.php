<?php

namespace App\CommandHandlers\OpenWeather;

use App\Models\Thesaurus\City;
use App\Services\Database\Logs\OpenWeatherWeatherService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class GetWeatherFromOpenWeatherCommandHandler
{
    /**
     * Отправляет запрос на сервер OpenWeather
     * 
     * @param City $city
     * @param OpenWeatherWeatherService $openWeatherWeatherService
     * @return Response
     */
    public function handle(City $city, OpenWeatherWeatherService $openWeatherWeatherService): Response
    {
        $openWeatherWeatherService->checkNumberOfWeatherLinesForLastMinuteLessBaseValue();
        $openWeatherWeatherService->checkTooEarlyToSubmitRequestForThisCity($city->id);
        
        return Http::get("http://api.openweathermap.org/data/2.5/weather".'?'.http_build_query([
                    'units' => 'metric',
                    'lang' => 'ru',
                    'id' => $city->open_weather_id,
                    'appid' => config('api.openweather_key')
                ]));
    }
}

<?php

namespace App\CommandHandlers\OpenWeather;

use App\Exceptions\OpenWeatherException;
use App\Models\Thesaurus\City;
use App\Repositories\OpenWeather\WeatherRepository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class GetWeatherFromOpenWeatherCommandHandler
{
    // Число запросов на сервер OpenWeather при бесплатном тарифе
    public const OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE = 60;
    // Период обновления погоды для города в минутах
    public const OPEN_WEATHER_CITY_UPDATE_PERIOD = 10;
    
    /**
     * Отправляет запрос на сервер OpenWeather
     * 
     * @param City $city
     * @param WeatherRepository $weatherRepository
     * @return Response
     * @throws OpenWeatherException
     */
    public function handle(City $city, WeatherRepository $weatherRepository): Response
    {
        if($weatherRepository->isTooEarlyToSubmitRequestForThisCity($city)) {
            throw new OpenWeatherException(trans('openweather.too_early_to_submit_request_for_this_city'));
        }
        
        if($weatherRepository->getNumberOfWeatherLinesForLastMinute() >= self::OPEN_WEATHER_LIMIT_FOR_ONE_MINUTE) {
            throw new OpenWeatherException(trans('openweather.request_limit_exceeded_for_one_minute'));
        }
        
        return Http::get("http://api.openweathermap.org/data/2.5/weather".'?'.http_build_query([
                    'units' => 'metric',
                    'lang' => 'ru',
                    'id' => $city->open_weather_id,
                    'appid' => config('api.openweather_key')
                ]));
    }
}

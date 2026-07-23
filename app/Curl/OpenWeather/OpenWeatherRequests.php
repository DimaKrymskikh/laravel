<?php

namespace App\Curl\OpenWeather;

use App\Models\Thesaurus\City;
use App\Services\CurlRequestInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Класс методов для отправки http-запросов на сервер OpenWeather
 */
class OpenWeatherRequests implements CurlRequestInterface
{
    /**
     * Отправляет http-запрос на сервер OpenWeather для одного города.
     * 
     * @param City $city
     * @return Response
     */
    public function getWeatherByCity(City $city): Response
    {
        return Http::get("http://api.openweathermap.org/data/2.5/weather".'?'.http_build_query([
                    'units' => 'metric',
                    'lang' => 'ru',
                    'id' => $city->open_weather_id,
                    'appid' => config('api.openweather_key')
                ]));
    }
}

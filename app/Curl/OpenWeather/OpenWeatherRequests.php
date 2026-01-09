<?php

namespace App\Curl\OpenWeather;

use App\Models\Thesaurus\City;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Класс методов для отправки http-запросов на сервер OpenWeather
 */
final class OpenWeatherRequests implements OpenWeatherRequestsInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
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

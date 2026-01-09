<?php

namespace App\Curl\OpenWeather;

use App\Models\Thesaurus\City;
use Illuminate\Http\Client\Response;

/**
 * Интерфейс методов для отправки http-запросов на сервер OpenWeather
 */
interface OpenWeatherRequestsInterface
{
    /**
     * Отправляет http-запрос на сервер OpenWeather для одного города.
     * 
     * @param City $city
     * @return Response
     */
    public function getWeatherByCity(City $city): Response;
}

<?php

namespace App\CommandHandlers\OpenWeather;

use App\Support\Url\Url;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GetWeatherFromOpenWeatherCommandHandler {
    use Url;
    
    /**
     * Отправляет запрос на сервер OpenWeather
     * 
     * @param int $cityOpenWeatherId
     * @return Response
     */
    public function handle(int $cityOpenWeatherId): Response
    {
        return Http::get($this->getUrl("http://api.openweathermap.org/data/2.5/weather", [
                'units' => 'metric',
                'lang' => 'ru',
                'id' => $cityOpenWeatherId,
                'appid' => config('api.openweather_key')
            ]));
    }
}

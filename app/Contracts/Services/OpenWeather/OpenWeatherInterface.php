<?php

namespace App\Contracts\Services\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Illuminate\Http\Client\Response;

interface OpenWeatherInterface
{
    public function sendRequest(City $city): Response;
    
    public function saveResponse(Response $response, City $city): Weather;
}

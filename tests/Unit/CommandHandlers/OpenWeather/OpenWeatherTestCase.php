<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\Contracts\Services\Database\Thesaurus\InfoAboutCitiesInterface;
use App\Contracts\Services\OpenWeather\OpenWeatherInterface;
use App\Console\Commands\OpenWeather\GetWeather;
use App\Models\Thesaurus\City;
use App\Support\Collections\Thesaurus\CityCollection;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Client\Response;

abstract class OpenWeatherTestCase extends TestCase
{
    protected function factoryCity(): City
    {
        $cityId = 7;
        $openWeatherId = 11;
        
        return City::factory()
                ->state([
                    'id' => $cityId,
                    'open_weather_id' => $openWeatherId,
                ])
                ->make();
    }
    
    protected function factoryCities(int $nCity): CityCollection
    {
        $cityId = 7;
        $openWeatherId = 11;
        
        return City::factory()
                ->count($nCity)
                ->state([
                    'id' => $cityId,
                    'open_weather_id' => $openWeatherId,
                ])
                ->make();
    }
    
    protected function defineSuccessStart(InfoAboutCitiesInterface $cityService, GetWeather $command): void
    {
        $city = $this->factoryCity();
        
        $this->defineIntArgument($city, $command);
        
        $cityService->method('getCityByOpenWeatherId')->willReturn($city);
    }
    
    protected function defineSuccessResponse(Response $response, OpenWeatherInterface $weatherService): void
    {
        $response->method('status')->willReturn(200);
        $response->method('object')->willReturn((object) []);
        
        $weatherService->method('sendRequest')->willReturn($response);
    }
    
    protected function defineFailResponse(Response $response, OpenWeatherInterface $weatherService): void
    {
        // Важно: статус не равен 200
        $response->method('status')->willReturn(400);
        
        $weatherService->method('sendRequest')->willReturn($response);
    }
    
    protected function defineIntArgument(City $city, GetWeather $command): void
    {
        $command->method('argument')->willReturn($city->open_weather_id);
    }
    
    protected function defineStringArgument(GetWeather $command): void
    {
        $command->method('argument')->willReturn('fail');
    }
    
    protected function defineNullArgument(GetWeather $command): void
    {
        $command->method('argument')->willReturn(null);
    }
}

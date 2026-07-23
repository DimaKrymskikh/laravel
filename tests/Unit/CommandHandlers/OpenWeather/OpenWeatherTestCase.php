<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\Console\Commands\OpenWeather\GetWeather;
use App\Models\Thesaurus\City;
use App\Queries\Thesaurus\CityQueries;
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
    
    protected function defineSuccessStart(CityQueries $cityQueries, GetWeather $command): void
    {
        $city = $this->factoryCity();
        
        $this->defineIntArgument($city, $command);
        
        $cityQueries->method('getByOpenWeatherId')->willReturn($city);
    }
    
    protected function defineSuccessResponse(Response $response): void
    {
        $response->method('status')->willReturn(200);
        
        $response->method('object')->willReturn((object) []);
    }
    
    protected function defineFailResponse(Response $response): void
    {
        // Важно: статус не равен 200
        $response->method('status')->willReturn(400);
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

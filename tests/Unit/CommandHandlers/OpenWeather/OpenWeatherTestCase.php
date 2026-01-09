<?php

namespace Tests\Unit\CommandHandlers\OpenWeather;

use App\CommandHandlers\OpenWeather\Facades\OpenWeatherFacadeInterface;
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
    
    protected function defineSuccessStart(OpenWeatherFacadeInterface $facade, GetWeather $command): City
    {
        $city = $this->factoryCity();
        
        $this->defineIntArgument($city, $command);
        
        $facade->expects($this->once())
                ->method('findCityByOpenWeatherId')
                ->willReturn($city);
        
        return $city;
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
    
    protected function defineNeverFacade(OpenWeatherFacadeInterface $facade): void
    {
        $facade->expects($this->never())
                ->method('getAllCitiesList');
        
        $facade->expects($this->never())
                ->method('checkNumberOfWeatherLinesForLastMinuteLessBaseValue');
        
        $facade->expects($this->never())
                ->method('checkTooEarlyToSubmitRequestForThisCity');
        
        $this->defineNeverRequest($facade);
    }
    
    protected function defineNeverRequest(OpenWeatherFacadeInterface $facade): void
    {
        $facade->expects($this->never())
                ->method('getWeatherByCity');
        
        $facade->expects($this->never())
                ->method('updateOrCreate');
    }
}

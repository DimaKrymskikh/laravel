<?php

namespace Tests\Unit\Services\Database\OpenWeather;

use App\Models\Thesaurus\City;
use App\Services\Database\Person\Dto\UserCityDto;
use Database\Testsupport\OpenWeather\OpenWeatherResponse;
use Illuminate\Http\Client\Response;
use PHPUnit\Framework\TestCase;

abstract class WeatherTestCase extends TestCase
{
    use OpenWeatherResponse;
    
    protected $cityId = 7;

    protected function getUserCityDto(): UserCityDto
    {
        $userId = 8;
        return new UserCityDto($userId, $this->cityId);
    }
    
    protected function factoryCity(): City
    {
        return City::factory()
                ->state([
                    'id' => $this->cityId,
                ])
                ->make();
    }
    
    protected function defineSuccessResponse(Response $response): void
    {
        $response->method('status')->willReturn(200);
        
        $response->method('object')->willReturn((object) $this->getWeatherForOneCity());
    }
    
}

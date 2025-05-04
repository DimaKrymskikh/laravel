<?php

namespace Tests\Unit\Events;

use App\Events\RefreshCityWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Modifiers\OpenWeather\Weather\WeatherModifiersInterface;
use App\Modifiers\Thesaurus\Cities\CityModifiersInterface;
use App\Queries\Person\Users\UserQueriesInterface;
use App\Queries\Thesaurus\Cities\CityQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\CityService;
use App\Services\Database\Thesaurus\TimezoneService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class RefreshCityWeatherTest extends TestCase
{
    private RefreshCityWeather $refreshCityWeather;
    private WeatherModifiersInterface $weatherModifiers;
    private CityModifiersInterface $cityModifiers;
    private UserQueriesInterface $userQueries;
    private CityQueriesInterface $cityQueries;
    private TimezoneQueriesInterface $timezoneQueries;
    private WeatherService $weatherService;
    private CityService $cityService;
    private TimezoneService $timezoneService;
    private int $cityId = 12;
    private int $userId = 23;
    
    public function test_weather_can_be_refresh(): void
    {
        $city = new City();
        $city->weather = new Weather();
        $city->weather->created_at = new CarbonImmutable();
        
        $this->cityQueries->expects($this->once())
                ->method('getById')
                ->with($this->cityId)
                ->willReturn($city);
        
        $broadcastWith = $this->refreshCityWeather->broadcastWith();
        $this->assertSame($city->weather, $broadcastWith['weather']);
        
        // Проверка имени канала
        $channelName = $this->refreshCityWeather->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
    }
    
    protected function setUp(): void
    {
        $this->weatherModifiers = $this->createMock(WeatherModifiersInterface::class);
        $this->userQueries = $this->createMock(UserQueriesInterface::class);
        
        $this->cityModifiers = $this->createMock(CityModifiersInterface::class);
        $this->cityQueries = $this->createMock(CityQueriesInterface::class);
        $this->cityService = new CityService($this->cityModifiers, $this->cityQueries);
        
        $this->timezoneQueries = $this->createMock(TimezoneQueriesInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneQueries);
        
        $this->weatherService = new WeatherService($this->weatherModifiers, $this->userQueries, $this->cityQueries, $this->timezoneService);
         
        $this->refreshCityWeather = new RefreshCityWeather($this->cityId, $this->userId, $this->weatherService, $this->cityService);
    }
}

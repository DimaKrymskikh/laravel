<?php

namespace Tests\Unit\Events;

use App\Events\RefreshCityWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\Repositories\OpenWeather\WeatherRepositoryInterface;
use App\Repositories\Person\UserRepositoryInterface;
use App\Repositories\Thesaurus\CityRepositoryInterface;
use App\Repositories\Thesaurus\TimezoneRepositoryInterface;
use App\Services\Database\OpenWeather\WeatherService;
use App\Services\Database\Thesaurus\CityService;
use App\Services\Database\Thesaurus\TimezoneService;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class RefreshCityWeatherTest extends TestCase
{
    private RefreshCityWeather $refreshCityWeather;
    private WeatherRepositoryInterface $weatherRepository;
    private UserRepositoryInterface $userRepository;
    private CityRepositoryInterface $cityRepository;
    private TimezoneRepositoryInterface $timezoneRepository;
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
        
        $this->cityRepository->expects($this->once())
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
        $this->weatherRepository = $this->createMock(WeatherRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        
        $this->cityRepository = $this->createMock(CityRepositoryInterface::class);
        $this->cityService = new CityService($this->cityRepository);
        
        $this->timezoneRepository = $this->createMock(TimezoneRepositoryInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneRepository);
        
        $this->weatherService = new WeatherService($this->weatherRepository, $this->userRepository, $this->cityRepository, $this->timezoneService);
         
        $this->refreshCityWeather = new RefreshCityWeather($this->cityId, $this->userId, $this->weatherService, $this->cityService);
    }
}

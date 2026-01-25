<?php

namespace Tests\Unit\Events;

use App\Events\RefreshCityWeather;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use App\ValueObjects\Broadcast\Weather\CurrentWeatherData;
use PHPUnit\Framework\TestCase;

class RefreshCityWeatherTest extends TestCase
{
    private RefreshCityWeather $refreshCityWeather;
    private CurrentWeatherData $weather;
    private int $userId = 23;
    
    public function test_weather_can_be_refresh(): void
    {
        $broadcastWith = $this->refreshCityWeather->broadcastWith();
        $this->assertSame($this->weather->data, $broadcastWith['weather']);
        
        // Проверка имени канала
        $channelName = $this->refreshCityWeather->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$this->userId");
    }
    
    protected function setUp(): void
    {
        $this->weather = CurrentWeatherData::create(Weather::factory()->make(), City::factory()->make());
        
        $this->refreshCityWeather = new RefreshCityWeather($this->userId, $this->weather);
    }
}

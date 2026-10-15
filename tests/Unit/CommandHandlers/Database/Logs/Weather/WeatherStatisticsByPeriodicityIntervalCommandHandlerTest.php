<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\Weather;

use App\Contracts\Database\DBqueriesInterface;
use App\CommandHandlers\Database\Logs\Weather\WeatherStatisticsByPeriodicityIntervalCommandHandler;

class WeatherStatisticsByPeriodicityIntervalCommandHandlerTest extends WeatherTestCase
{
    private WeatherStatisticsByPeriodicityIntervalCommandHandler $handler;
    private DBqueriesInterface $queries;
    
    public function test_success_handle(): void
    {
        
        $weatherStatisticsDto = $this->getWeatherStatisticsDto();
        
        $this->queries->method('getArray')
                ->willReturn($this->getWeatherIntervalsStatistics());
        
        $this->queries->method('getObject')
                ->willReturn($this->getWeatherAllStatistics());
        
        $this->assertIsObject($this->handler->handle($weatherStatisticsDto));
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createStub(DBqueriesInterface::class);
        
        $this->handler = new WeatherStatisticsByPeriodicityIntervalCommandHandler($this->queries);
    }
}

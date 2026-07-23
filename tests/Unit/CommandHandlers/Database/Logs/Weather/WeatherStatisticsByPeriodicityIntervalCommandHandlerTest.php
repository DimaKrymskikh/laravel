<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\Weather;

use App\CommandHandlers\Database\Logs\Weather\WeatherStatisticsByPeriodicityIntervalCommandHandler;
use App\Queries\Logs\OpenWeatherWeatherQueries;
use App\Services\ServiceManagerInterface;

class WeatherStatisticsByPeriodicityIntervalCommandHandlerTest extends WeatherTestCase
{
    private ServiceManagerInterface $serviceManager;
    private WeatherStatisticsByPeriodicityIntervalCommandHandler $handler;
    private OpenWeatherWeatherQueries $queries;
    
    public function test_success_handle(): void
    {
        
        $weatherStatisticsDto = $this->getWeatherStatisticsDto();
        
        $this->queries->expects($this->once())
                ->method('getArray')
                ->willReturn($this->getWeatherIntervalsStatistics());
        
        $this->queries->expects($this->once())
                ->method('getObject')
                ->willReturn($this->getWeatherAllStatistics());
        
        $this->assertIsObject($this->handler->handle($weatherStatisticsDto));
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(OpenWeatherWeatherQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->queries);
        
        $this->handler = new WeatherStatisticsByPeriodicityIntervalCommandHandler($this->serviceManager);
    }
}

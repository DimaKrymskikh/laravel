<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\Weather;

use App\CommandHandlers\Database\Logs\Weather\WeatherStatisticsByPeriodicityIntervalCommandHandler;
use App\Queries\Logs\OpenWeatherWeather\OpenWeatherWeatherQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;

class WeatherStatisticsByPeriodicityIntervalCommandHandlerTest extends WeatherTestCase
{
    private WeatherStatisticsByPeriodicityIntervalCommandHandler $handler;
    private OpenWeatherWeatherQueriesInterface $queries;
    private TimezoneQueriesInterface $timezoneQueries;
    private TimezoneService $timezoneService;
    
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
        $this->queries = $this->createMock(OpenWeatherWeatherQueriesInterface::class);
        $this->timezoneQueries = $this->createMock(TimezoneQueriesInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneQueries);
        
        $this->handler = new WeatherStatisticsByPeriodicityIntervalCommandHandler($this->queries, $this->timezoneService);
    }
}

<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\Weather;

use App\CommandHandlers\Database\Logs\Weather\WeatherListForPageCommandHandler;
use App\Queries\Logs\OpenWeatherWeather\WeatherListForPage\WeatherListForPageQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Pagination\LengthAwarePaginator;

class WeatherListForPageCommandHandlerTest extends WeatherTestCase
{
    private WeatherListForPageCommandHandler $handler;
    private WeatherListForPageQueriesInterface $queries;
    private TimezoneQueriesInterface $timezoneQueries;
    private TimezoneService $timezoneService;

    public function test_success_handle(): void
    {
        $city = $this->getCity($this->cityId);
        
        $weatherFilterDto = $this->getWeatherFilterDto();
        $paginatorDto = $this->getPaginatorDto();
        
        $this->queries->expects($this->once())
                ->method('get')
                ->with($paginatorDto, $weatherFilterDto, $this->identicalTo($this->cityId));
        
        $this->assertInstanceOf(LengthAwarePaginator::class, $this->handler->handle($paginatorDto, $weatherFilterDto, $city));
    }
    
    protected function setUp(): void
    {
        $this->queries = $this->createMock(WeatherListForPageQueriesInterface::class);
        $this->timezoneQueries = $this->createMock(TimezoneQueriesInterface::class);
        $this->timezoneService = new TimezoneService($this->timezoneQueries);
        
        $this->handler = new WeatherListForPageCommandHandler($this->queries, $this->timezoneService);
    }
}

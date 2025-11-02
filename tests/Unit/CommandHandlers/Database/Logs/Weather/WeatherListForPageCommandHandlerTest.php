<?php

namespace Tests\Unit\CommandHandlers\Database\Logs\Weather;

use App\CommandHandlers\Database\Logs\Weather\WeatherListForPageCommandHandler;
use App\Models\Thesaurus\City;
use App\Queries\Logs\OpenWeatherWeather\WeatherListForPage\WeatherListForPageQueriesInterface;
use App\Queries\Thesaurus\Timezones\TimezoneQueriesInterface;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Unit\TestCase\WeatherTestCase;

class WeatherListForPageCommandHandlerTest extends WeatherTestCase
{
    private WeatherListForPageCommandHandler $handler;
    private WeatherListForPageQueriesInterface $queries;
    private TimezoneQueriesInterface $timezoneQueries;
    private TimezoneService $timezoneService;
    private int $cityId = 18;

    public function test_success_handle(): void
    {
        $city = new City();
        $city->id = $this->cityId;
        
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
